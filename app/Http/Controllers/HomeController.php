<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\DbHelper;
use App\Models\ActivityLog;

use App\Models\Session;
use App\Models\FeeStructure2;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->type === 'admin';
        $sessionId = $user->active_session_id;
        $session = $sessionId ? Session::find($sessionId) : null;
        $sessionName = $session?->session_name ?? 'Not Assigned';

        // ── Core counts for Active Session ───────────────────────────
        $totalStudents = Student::where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId)
                  ->orWhereIn('id', function ($sub) use ($sessionId) {
                      $sub->select('student_id')->from('student_courses')->where('session_id', $sessionId);
                  });
            })
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->count();

        $totalStaff = Staff::count();

        // ── Fee totals for Active Session ────────────────────────────
        $totalCollected = (float) (FeePayment::where('session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('amount') ?? 0);

        $todayCollection = (float) (FeePayment::where('session_id', $sessionId)
            ->where(function ($q) {
                $q->whereDate('payment_date', today())
                  ->orWhereDate('created_at', today());
            })
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('amount') ?? 0);

        $monthCollection = (float) (FeePayment::where('session_id', $sessionId)
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereMonth('payment_date', now()->month)
                       ->whereYear('payment_date', now()->year);
                })->orWhere(function ($sq) {
                    $sq->whereMonth('created_at', now()->month)
                       ->whereYear('created_at', now()->year);
                });
            })
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('amount') ?? 0);

        $totalDiscounts = (float) (FeePayment::where('session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('discount_amount') ?? 0);

        // ── Accounts (Income & Expenses) for Active Session ──────────
        $totalExpense = (float) (Expense::where('session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('amount') ?? 0);

        $otherIncome = (float) (Income::where('session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->sum('amount') ?? 0);

        // Net income = Gross revenue (Fees collected + Other income) minus Total expenses
        $totalGrossInflow = $totalCollected + $otherIncome;
        $netIncome = $totalGrossInflow - $totalExpense;

        // ── Expected Fees & Outstanding Balance for Active Session ───
        $enrolledStudents = DB::table('student_courses')
            ->join('students', 'students.id', '=', 'student_courses.student_id')
            ->where('student_courses.session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('students.user_id', $user->id))
            ->select('students.id as student_id', DB::raw(DbHelper::castInt('students.course') . ' as course_id'), 'student_courses.semaster')
            ->get();

        $feeMatrix = FeeStructure2::where('session_id', $sessionId)->get();
        $totalExpectedFee = 0.0;
        foreach ($enrolledStudents as $enrolled) {
            $semNum = max(1, min(8, (int) ($enrolled->semaster ?: 1)));
            $semCol = 'sem' . $semNum;
            $courseFee = (float) $feeMatrix->where('course_id', (int) $enrolled->course_id)->sum($semCol);
            $totalExpectedFee += $courseFee;
        }

        $totalDue = max(0, $totalExpectedFee - $totalCollected - $totalDiscounts);

        // ── Monthly fee collection — last 6 months in Active Session ─
        $monthlyData = FeePayment::where('session_id', $sessionId)
            ->selectRaw(
                DbHelper::monthLabel('created_at') . " as label,
                 " . DbHelper::yearMonth('created_at') . " as sort_key,
                 SUM(amount) as total"
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->groupBy('label', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $chartLabels = $monthlyData->pluck('label')->toArray();
        $chartData   = $monthlyData->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // Fill missing months (show 0 if no data)
        if (empty($chartLabels)) {
            for ($i = 5; $i >= 0; $i--) {
                $chartLabels[] = now()->subMonths($i)->format('M');
                $chartData[]   = 0;
            }
        }

        // ── Course-wise student count in Active Session ──────────────
        $courseWise = DB::table('students')
            ->join('courses', 'courses.id', '=', DB::raw(DbHelper::castInt('students.course')))
            ->join('student_courses', 'student_courses.student_id', '=', 'students.id')
            ->where('student_courses.session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('students.user_id', $user->id))
            ->selectRaw('courses.name as course_name, COUNT(DISTINCT students.id) as cnt')
            ->groupBy('courses.name')
            ->orderByDesc('cnt')
            ->get();

        // ── Payment method breakdown in Active Session ───────────────
        $paymentBreakdown = FeePayment::where('session_id', $sessionId)
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as cnt')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        // ── Recent 6 payments in Active Session ──────────────────────
        $recentPayments = FeePayment::where('session_id', $sessionId)
            ->with(['student', 'course'])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        // ── Gender breakdown in Active Session ───────────────────────
        $genderData = DB::table('students')
            ->join('student_courses', 'student_courses.student_id', '=', 'students.id')
            ->where('student_courses.session_id', $sessionId)
            ->when(!$isAdmin, fn($q) => $q->where('students.user_id', $user->id))
            ->selectRaw("COALESCE(NULLIF(students.gender,''), 'Unknown') as gender, COUNT(DISTINCT students.id) as cnt")
            ->groupBy('gender')
            ->get();

        return view('home', compact(
            'sessionName', 'totalStudents', 'totalStaff',
            'totalCollected', 'todayCollection', 'monthCollection', 'totalDiscounts',
            'totalExpense', 'otherIncome', 'totalGrossInflow', 'netIncome',
            'totalExpectedFee', 'totalDue',
            'chartLabels', 'chartData',
            'courseWise', 'paymentBreakdown', 'recentPayments', 'genderData'
        ));
    }
    
    
    
    // DASHBOARD END
    
         public function library_issue_book()
    {
         
        return view('admin.library.issue_book');
    }
     public function borrower()
    {
         
        return view('admin.library.borrower');
    }
    public function activity_logs(Request $request)
    {
        // Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = ActivityLog::query();

        if ($request->filled('date')) {
            try {
                $query->whereDate('created_at', Carbon::parse($request->date)->format('Y-m-d'));
            } catch (\Exception $e) {}
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $activityStats = [
            'total' => (clone $query)->count(),
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'users' => (clone $query)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'month' => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
        $logs    = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $users   = User::orderBy('name')->get(['id', 'name', 'type']);
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('admin.activity_logs', compact('logs', 'users', 'modules', 'activityStats'));
    }

}
