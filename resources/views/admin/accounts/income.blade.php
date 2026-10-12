@extends('layouts.appadmin')

@section('content')
<div class="premium-page accounts-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Accounts</span><h1>Income</h1><p>Record and review all non-fee income entries.</p></div>
        <button type="button" class="premium-btn premium-btn-gold" data-bs-toggle="modal" data-bs-target="#addIncomeModal"><i class="fa fa-plus"></i> Add income</button>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="premium-content-card accounts-ledger-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Income ledger</span><h2>Recorded income <span class="premium-count-badge">{{ $incomes->count() }}</span></h2></div><span class="accounts-total accounts-total--income">₹{{ number_format($incomes->sum('amount'), 2) }}</span></div>
        <div class="premium-table-wrap"><table class="premium-table"><thead><tr><th>Date</th><th>Category</th><th class="text-end">Amount</th><th>Description</th><th class="text-end">Action</th></tr></thead><tbody>@forelse($incomes as $income)<tr><td>{{ $income->date->format('d-m-Y') }}</td><td><span class="accounts-category-badge">{{ $income->category }}</span></td><td class="text-end finance-amount">₹{{ number_format($income->amount, 2) }}</td><td>{{ $income->description ?: '-' }}</td><td class="text-end"><a href="{{ route('accounts.income.delete', $income->id) }}" class="premium-btn premium-btn-danger premium-btn-sm"><i class="fa fa-trash"></i> Delete</a></td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No income entries recorded yet.</td></tr>@endforelse</tbody></table></div>
    </section>
</div>

<div class="modal fade" id="addIncomeModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content premium-modal"><div class="modal-header"><div><span class="premium-eyebrow">Accounts</span><h5>Add income</h5></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('accounts.save_income') }}">@csrf<div class="modal-body"><div class="finance-field"><label for="incomeCategory">Category <span>*</span></label><input id="incomeCategory" name="category" required></div><div class="finance-field"><label for="incomeAmount">Amount <span>*</span></label><input id="incomeAmount" name="amount" type="number" step="0.01" min="0" required></div><div class="finance-field"><label for="incomeDate">Date <span>*</span></label><input id="incomeDate" name="date" type="text" class="premium-datepicker" value="{{ now()->format('d-m-Y') }}" required></div><div class="finance-field"><label for="incomeDescription">Description</label><textarea id="incomeDescription" name="description" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Cancel</button><button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-save"></i> Save income</button></div></form></div></div></div>
@endsection
