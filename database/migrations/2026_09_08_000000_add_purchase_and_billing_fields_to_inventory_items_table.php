<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('bill_no')->nullable()->after('sku');
            $table->date('purchase_date')->nullable()->after('bill_no');
            $table->string('party_name')->nullable()->after('purchase_date');
            $table->string('supplier_name')->nullable()->after('party_name');
            $table->text('supplier_address')->nullable()->after('supplier_name');
            $table->string('payment_method')->nullable()->after('supplier_address');
            $table->string('cheque_no')->nullable()->after('payment_method');
            $table->string('transaction_id')->nullable()->after('cheque_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn([
                'bill_no',
                'purchase_date',
                'party_name',
                'supplier_name',
                'supplier_address',
                'payment_method',
                'cheque_no',
                'transaction_id',
            ]);
        });
    }
};
