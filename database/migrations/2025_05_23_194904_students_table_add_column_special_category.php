<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentsTableAddColumnSpecialCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('special_category')->nullable()->after('id');
            $table->foreign('special_category')->references('id')->on('special_category_fee_heads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            //
            $table->dropForeign(['special_category']);
            $table->dropColumn('special_category');
        });
    }
}
