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
        // 1. Families Table
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('family_no')->unique();
            $table->string('father_name')->nullable();
            $table->string('father_cnic')->nullable()->index();
            $table->string('mother_name')->nullable();
            $table->string('mother_cnic')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 2. Add family_id to students
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('family_id')->nullable()->after('tenant_id');
            $table->foreign('family_id')->references('id')->on('families')->onDelete('set null');
        });

        // 3. Student Visitors Table
        Schema::create('student_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->string('name');
            $table->string('relation');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });

        // 4. Student Documents / Attachments Table (Strict Normalization)
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_visitors');
        
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
        });

        Schema::dropIfExists('families');
    }
};
