<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialCategoryFeeHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_category_fee_heads', function (Blueprint $table) {
            $table->id();
               $table->foreignId('special_category_id')->constrained('special_categories')->onDelete('cascade');
            $table->string('fee_head_id');
            $table->enum('discount_type', ['Flat', 'Percentage']);
            $table->decimal('discount', 10, 2);
            $table->timestamps();

            $table->foreign('fee_head_id')->references('fee_head_id')->on('fee_structures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_category_fee_heads');
    }
}
