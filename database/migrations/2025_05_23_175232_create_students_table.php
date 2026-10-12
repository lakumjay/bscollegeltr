<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('roll_no')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('date_of_birth');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('course');
            $table->string('batch'); // e.g., 2023-2027
            $table->date('admission_date');
            $table->integer('current_year'); // 1, 2, 3, 4
            $table->integer('current_semester'); // 1–8
            $table->string('status'); // e.g., Active, Inactive
            $table->unsignedBigInteger('session_id');
            $table->json('subjects')->nullable();

            $table->string('photo')->nullable();
            $table->string('aadhar_card')->nullable();
            $table->string('id_proof')->nullable(); // e.g., PAN, Voter ID
            $table->string('category')->nullable(); // e.g., General, OBC, SC, ST
            $table->string('caste')->nullable();
            $table->string('category_certificate')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
