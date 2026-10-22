<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of sessions.
     */
    public function index()
    {
        $sessions = Session::all()->sort(function ($a, $b) {
            return strnatcasecmp($b->session_name, $a->session_name);
        })->values();

        $currentSessionId = User::query()->whereNotNull('active_session_id')->value('active_session_id')
            ?? Auth::user()->active_session_id;

        return view('admin.sessions', compact('sessions', 'currentSessionId'));
    }

    /**
     * Convert any 4-year session format (e.g. 2026-2030) into its canonical 2-digit format (2026-30).
     */
    public static function canonicalSessionName($name)
    {
        $norm = trim(preg_replace('/\s*-\s*/', '-', (string)$name));
        $norm = trim(preg_replace('/\s+/', ' ', $norm));
        if (preg_match('/^(\d{4})-(\d{2})(\d{2})$/', $norm, $m) && substr($m[1], 0, 2) === $m[2]) {
            return $m[1] . '-' . $m[3];
        }
        return $norm;
    }

    /**
     * Store a newly created academic session with duplicate prevention.
     */
    public function store(Request $request)
    {
        $raw = trim((string) $request->session_name);
        $normalized = trim(preg_replace('/\s*-\s*/', '-', $raw));
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized));

        $request->merge(['session_name' => $normalized]);

        $request->validate([
            'session_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sessions', 'session_name'),
            ],
        ], [
            'session_name.required' => 'Session name is required.',
            'session_name.unique' => "Session '{$normalized}' already exists.",
        ]);

        // Canonical check (prevents 2026-2030 if 2026-30 exists, and vice versa)
        $canonical = self::canonicalSessionName($normalized);
        foreach (Session::all() as $s) {
            if (self::canonicalSessionName($s->session_name) === $canonical) {
                return redirect()->to(url('admin/sessions'))->with('error', "Session '{$s->session_name}' already exists for this academic period.");
            }
        }

        $session = Session::create(['session_name' => $normalized]);

        return redirect()->to(url('admin/sessions'))->with('success', "Academic session '{$session->session_name}' created successfully.");
    }

    /**
     * Update an academic session name.
     */
    public function update(Request $request, $id)
    {
        $session = Session::findOrFail($id);
        $raw = trim((string) $request->session_name);
        $normalized = trim(preg_replace('/\s*-\s*/', '-', $raw));
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized));

        $request->merge(['session_name' => $normalized]);

        $request->validate([
            'session_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sessions', 'session_name')->ignore($session->id),
            ],
        ], [
            'session_name.required' => 'Session name is required.',
            'session_name.unique' => "Session '{$normalized}' already exists.",
        ]);

        $oldName = $session->session_name;
        $session->update(['session_name' => $normalized]);

        // Keep students batch in sync if batch was oldName
        DB::table('students')->where('batch', $oldName)->update(['batch' => $normalized]);

        return redirect()->to(url('admin/sessions'))->with('success', "Academic session updated to '{$normalized}' successfully.");
    }

    /**
     * Delete an academic session safely.
     */
    public function destroy($id)
    {
        $session = Session::findOrFail($id);
        $activeSessionId = User::query()->whereNotNull('active_session_id')->value('active_session_id')
            ?? Auth::user()->active_session_id;

        if ((int)$session->id === (int)$activeSessionId) {
            return redirect()->to(url('admin/sessions'))->with('error', "Cannot delete currently active session '{$session->session_name}'. Please activate another session first.");
        }

        $fallback = Session::where('id', '!=', $session->id)->first();
        if ($fallback) {
            $tables = ['students', 'student_courses', 'fee_records', 'fee_payments', 'fee_structures', 'incomes', 'expenses', 'marks'];
            foreach ($tables as $tbl) {
                if (\Illuminate\Support\Facades\Schema::hasTable($tbl) && \Illuminate\Support\Facades\Schema::hasColumn($tbl, 'session_id')) {
                    DB::table($tbl)->where('session_id', $session->id)->update(['session_id' => $fallback->id]);
                }
            }
        }

        $deletedName = $session->session_name;
        $session->delete();

        return redirect()->to(url('admin/sessions'))->with('success', "Academic session '{$deletedName}' deleted successfully.");
    }

    /**
     * Automatically merge equivalent session formats (e.g. 2026-2030 into 2026-30).
     */
    public function mergeDuplicates()
    {
        $all = Session::all();
        $mergedCount = 0;
        $groups = [];

        foreach ($all as $s) {
            $key = self::canonicalSessionName($s->session_name);
            $groups[$key][] = $s;
        }

        $activeSessionId = Auth::user()->active_session_id;

        foreach ($groups as $canonKey => $items) {
            if (count($items) > 1) {
                // Find primary: prefer active session, or matching canonical 2-digit format
                $primary = collect($items)->firstWhere('id', $activeSessionId)
                    ?? collect($items)->firstWhere('session_name', $canonKey)
                    ?? $items[0];

                $tables = ['students', 'student_courses', 'fee_records', 'fee_payments', 'fee_structures', 'incomes', 'expenses', 'marks'];

                foreach ($items as $item) {
                    if ($item->id !== $primary->id) {
                        foreach ($tables as $tbl) {
                            if (\Illuminate\Support\Facades\Schema::hasTable($tbl) && \Illuminate\Support\Facades\Schema::hasColumn($tbl, 'session_id')) {
                                DB::table($tbl)->where('session_id', $item->id)->update(['session_id' => $primary->id]);
                            }
                        }
                        if (\Illuminate\Support\Facades\Schema::hasTable('users') && \Illuminate\Support\Facades\Schema::hasColumn('users', 'active_session_id')) {
                            DB::table('users')->where('active_session_id', $item->id)->update(['active_session_id' => $primary->id]);
                        }
                        // Update batch text in students if matching duplicate name
                        DB::table('students')->where('batch', $item->session_name)->update(['batch' => $primary->session_name]);

                        $item->delete();
                        $mergedCount++;
                    }
                }
            }
        }

        return redirect()->to(url('admin/sessions'))->with('success', "Duplicate sessions merged successfully! ({$mergedCount} duplicates cleaned up).");
    }

    /**
     * Activate a session for the authenticated user.
     */
    public function activate($id)
    {
        $session = Session::findOrFail($id);
        // The active academic session is shared by the institution. Historical
        // records retain their own session_id and are not changed here.
        DB::transaction(function () use ($session) {
            User::query()->update(['active_session_id' => $session->id]);
        });

        return redirect()->to(url('admin/sessions'))->with('success', 'Active academic session updated for all users.');
    }
}
