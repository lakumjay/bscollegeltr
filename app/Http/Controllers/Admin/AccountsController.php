<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\FeeRecord;
use App\Models\Income;
use App\Models\Profile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    public function income(Request $request)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $incomes = Income::where('session_id', $currentSession)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.accounts.income', compact('incomes'));
    }

    public function saveIncome(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $currentSession = $user->active_session_id;

        Income::create([
            'user_id' => $user->id,
            'session_id' => $currentSession,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'description' => $request->description,
        ]);

        return redirect()->route('accounts.income')
            ->with('success', 'Income added successfully!');
    }

    public function expense(Request $request)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $expenses = Expense::where('session_id', $currentSession)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.accounts.expense', compact('expenses'));
    }

    public function saveExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $currentSession = $user->active_session_id;

        Expense::create([
            'user_id' => $user->id,
            'session_id' => $currentSession,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'description' => $request->description,
        ]);

        return redirect()->route('accounts.expense')
            ->with('success', 'Expense added successfully!');
    }
    public function expense_delete(Request $request,$id){
       Expense::where('id',$id)->delete();
        return redirect()->route('accounts.expense')
            ->with('success', 'Expense deleted successfully!');  
    }
     public function income_delete(Request $request,$id){
       Income::where('id',$id)->delete();
        return redirect()->route('accounts.income')
            ->with('success', 'Income deleted successfully!');  
    }

    public function daybook(Request $request)
    {
        [$transactions, $fromDate, $toDate] = $this->daybookReportData($request);

        return view('admin.accounts.daybook', compact('transactions', 'fromDate', 'toDate'));
    }

    public function exportDaybookCsv(Request $request)
    {
        [$transactions, $fromDate, $toDate] = $this->daybookReportData($request);
        $filename = "daybook_{$fromDate}_to_{$toDate}.csv";

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Student Name',
                'Student Category',
                'Date',
                'Type',
                'Amount',
                'Short Description',
                'Recorded By',
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction['student_name'],
                    $transaction['student_type'] ?? '-',
                    Carbon::parse($transaction['date'])->format('d-m-Y'),
                    $transaction['type'],
                    number_format((float) $transaction['amount'], 2, '.', ''),
                    $transaction['description'] ?: ($transaction['category'] ?: '-'),
                    $transaction['user'],
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportDaybookPdf(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        [$transactions, $fromDate, $toDate] = $this->daybookReportData($request);
        $profile = Profile::first();
        $logoData = null;

        if ($profile?->logo && file_exists(public_path($profile->logo))) {
            $logoPath = public_path($profile->logo);
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.accounts.daybook_pdf', compact(
            'transactions', 'fromDate', 'toDate', 'profile', 'logoData'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("daybook_{$fromDate}_to_{$toDate}.pdf");
    }

    private function daybookReportData(Request $request): array
    {
        $currentSession = Auth::user()->active_session_id;

        $fromInput = $request->input('from_date');
        $toInput = $request->input('to_date');

        try {
            $fromDate = $fromInput ? Carbon::parse($fromInput)->format('Y-m-d') : Carbon::today()->subDays(30)->format('Y-m-d');
        } catch (\Exception $e) {
            $fromDate = Carbon::today()->subDays(30)->format('Y-m-d');
        }

        try {
            $toDate = $toInput ? Carbon::parse($toInput)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        } catch (\Exception $e) {
            $toDate = Carbon::today()->format('Y-m-d');
        }

        if ($fromDate > $toDate) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
        }

        $feePayments = FeePayment::where('session_id', $currentSession)
            ->whereDate('payment_date', '>=', $fromDate)
            ->whereDate('payment_date', '<=', $toDate)
            ->with(['student', 'user'])
            ->get()
            ->map(function ($payment) {
                $studentType = $payment->student_type ?? 'Regular College';
                if ($studentType === 'Other College' && !empty($payment->other_college_name)) {
                    $studentTypeDisplay = 'Other College (' . $payment->other_college_name . ')';
                } else {
                    $studentTypeDisplay = $studentType;
                }

                return [
                    'date' => Carbon::parse($payment->payment_date)->format('Y-m-d'),
                    'type' => 'Fee',
                    'student_type' => $studentTypeDisplay,
                    'raw_student_type' => $payment->student_type ?? 'Regular College',
                    'other_college_name' => $payment->other_college_name,
                    'category' => 'Fee',
                    'amount' => (float) $payment->amount,
                    'description' => $payment->notes ?? 'Fee payment',
                    'student_name' => $payment->student
                        ? trim($payment->student->first_name . ' ' . $payment->student->last_name)
                        : '-',
                    'student_id' => $payment->student?->student_id ?? '-',
                    'user' => $payment->user?->name ?? '-',
                ];
            });

        $incomes = Income::where('session_id', $currentSession)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->with('user')
            ->get()
            ->map(function ($income) {
                return [
                    'date' => Carbon::parse($income->date)->format('Y-m-d'),
                    'type' => 'Income',
                    'student_type' => '-',
                    'raw_student_type' => null,
                    'other_college_name' => null,
                    'category' => $income->category,
                    'amount' => (float) $income->amount,
                    'description' => $income->description ?? 'Income entry',
                    'student_name' => '-',
                    'student_id' => '-',
                    'user' => $income->user?->name ?? '-',
                ];
            });

        $expenses = Expense::where('session_id', $currentSession)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->with('user')
            ->get()
            ->map(function ($expense) {
                return [
                    'date' => Carbon::parse($expense->date)->format('Y-m-d'),
                    'type' => 'Expense',
                    'student_type' => '-',
                    'raw_student_type' => null,
                    'other_college_name' => null,
                    'category' => $expense->category,
                    'amount' => -(float) $expense->amount,
                    'description' => $expense->description ?? 'Expense entry',
                    'student_name' => '-',
                    'student_id' => '-',
                    'user' => $expense->user?->name ?? '-',
                ];
            });

        $transactions = collect($feePayments)->merge($incomes)->merge($expenses)
            ->sortByDesc('date')
            ->values();

        return [$transactions, $fromDate, $toDate];
    }
}
