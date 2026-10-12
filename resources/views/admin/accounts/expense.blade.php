@extends('layouts.appadmin')

@section('content')
<div class="premium-page premium-page--compact accounts-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Accounts</span><h1>Expenses</h1><p>Track operational expenses in one clear, auditable ledger.</p></div>
        <button type="button" class="premium-btn premium-btn-gold" data-bs-toggle="modal" data-bs-target="#addExpenseModal"><i class="fa fa-plus"></i> Add expense</button>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="premium-content-card accounts-ledger-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Expense ledger</span><h2>Recorded expenses <span class="premium-count-badge">{{ $expenses->count() }}</span></h2></div><span class="accounts-total accounts-total--expense">₹{{ number_format($expenses->sum('amount'), 2) }}</span></div>
        <div class="premium-table-wrap"><table class="premium-table"><thead><tr><th>Date</th><th>Category</th><th class="text-end">Amount</th><th>Description</th><th class="text-end">Action</th></tr></thead><tbody>@forelse($expenses as $expense)<tr><td>{{ $expense->date->format('d-m-Y') }}</td><td><span class="accounts-category-badge">{{ $expense->category }}</span></td><td class="text-end accounts-expense-amount">₹{{ number_format($expense->amount, 2) }}</td><td>{{ $expense->description ?: '-' }}</td><td class="text-end"><a href="{{ route('accounts.expense.delete', $expense->id) }}" class="premium-btn premium-btn-danger premium-btn-sm"><i class="fa fa-trash"></i> Delete</a></td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No expense entries recorded yet.</td></tr>@endforelse</tbody></table></div>
    </section>
</div>

<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content premium-modal"><div class="modal-header"><div><span class="premium-eyebrow">Accounts</span><h5>Add expense</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('accounts.save_expense') }}">@csrf<div class="modal-body"><div class="finance-field"><label for="expenseCategory">Category <span>*</span></label><input id="expenseCategory" name="category" required></div><div class="finance-field"><label for="expenseAmount">Amount <span>*</span></label><input id="expenseAmount" name="amount" type="number" step="0.01" min="0" required></div><div class="finance-field"><label for="expenseDate">Date <span>*</span></label><input id="expenseDate" name="date" type="text" class="premium-datepicker" value="{{ now()->format('d-m-Y') }}" required></div><div class="finance-field"><label for="expenseDescription">Description</label><textarea id="expenseDescription" name="description" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Cancel</button><button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-save"></i> Save expense</button></div></form></div></div></div>
@endsection
