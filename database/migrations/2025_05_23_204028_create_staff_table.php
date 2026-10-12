<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('role')->nullable(); // e.g., Teacher, Admin, Librarian
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('currency')->default('INR');
            $table->date('joining_date')->nullable();
            $table->string('qualification')->nullable();
            $table->string('photo')->nullable(); // Path to photo
            $table->string('id_proof')->nullable(); // Path to ID proof (e.g., Aadhar, Passport)
            $table->string('certificate')->nullable(); // Path to educational certificate
            $table->string('status')->default('Active'); // Active, Inactive, Terminated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
