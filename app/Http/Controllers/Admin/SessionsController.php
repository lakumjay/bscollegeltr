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
     * Store a newly created academic session with duplicate prevention.
     */
    public function store(Request $request)
    {
        $raw = trim((string) $request->session_name);
        // Normalize whitespace and spaced dashes: e.g. "2026 - 2030" -> "2026-2030"
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

        // Secondary guard: case-insensitive check
        $existing = Session::whereRaw('LOWER(REPLACE(session_name, " ", "")) = ?', [strtolower(str_replace(' ', '', $normalized))])->first();
        if ($existing) {
            return redirect()->to(url('admin/sessions'))->with('error', "Session '{$existing->session_name}' already exists.");
        }

        $session = Session::create(['session_name' => $normalized]);

        return redirect()->to(url('admin/sessions'))->with('success', "Academic session '{$session->session_name}' created successfully.");
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
