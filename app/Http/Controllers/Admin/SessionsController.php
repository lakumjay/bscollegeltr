<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;

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
        $sessions = Session::orderByDesc('id')->get();
        $currentSessionId = User::query()->whereNotNull('active_session_id')->value('active_session_id')
            ?? Auth::user()->active_session_id;

        return view('admin.sessions', compact('sessions', 'currentSessionId'));
    }

    /**
     * Store a newly created academic session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string|max:50',
        ]);

        $name = trim((string) $request->session_name);
        $session = Session::firstOrCreate(['session_name' => $name]);

        return redirect()->to(url('admin/sessions'))->with('success', "Academic session '{$session->session_name}' is ready.");
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
