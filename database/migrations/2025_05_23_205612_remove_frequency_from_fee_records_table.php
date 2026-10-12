<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFrequencyFromFeeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
    {
        Schema::table('fee_records', function (Blueprint $table) {
            $table->dropColumn('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('fee_records', function (Blueprint $table) {
            $table->enum('frequency', ['Monthly', 'Quarterly', 'Yearly'])->after('net_amount');
        });
    }
}
