<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            
            // Academic Context
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('school_class_id');
            $table->unsignedBigInteger('section_id');

            // Primary Identification
            $table->string('admission_no')->unique();
            $table->string('roll_no')->nullable();
            $table->string('cnic_no')->nullable(); // B-Form or CNIC
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('nationality')->nullable()->default('Pakistani');
            $table->string('mother_tongue')->nullable();
            $table->string('disability')->nullable();
            $table->string('previous_school')->nullable();
            
            // Father Information
            $table->string('father_name');
            $table->string('father_cnic')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            
            // Mother Information
            $table->string('mother_name')->nullable();
            $table->string('mother_cnic')->nullable();
            
            // Guardian Information
            $table->string('guardian_name')->nullable();
            $table->string('guardian_cnic')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->decimal('guardian_annual_income', 15, 2)->nullable();
            
            // Contact & Logistics
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_transport_required')->default(false);
            $table->boolean('is_hostel_required')->default(false);
            
            // Meta
            $table->date('admission_date');
            $table->boolean('is_active')->default(true);
            $table->string('student_image')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();

            // Relationships
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->foreign('campus_id')->references('id')->on('campuses');
            $table->foreign('school_class_id')->references('id')->on('school_classes');
            $table->foreign('section_id')->references('id')->on('sections');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
