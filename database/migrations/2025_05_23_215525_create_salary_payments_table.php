<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('INR');
            $table->enum('status', ['Paid', 'Pending', 'Failed'])->default('Pending');
            $table->string('month_year', 7); // e.g., "2025-05"
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'month_year']); // Prevent duplicate payments for same staff/month
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_payments');
    }
}
