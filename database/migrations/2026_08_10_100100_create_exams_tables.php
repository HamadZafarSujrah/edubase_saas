<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('school_class_id');
            $table->string('name');
            $table->enum('type', ['monthly', 'mid', 'final', 'quiz', 'assignment'])->default('mid');
            $table->date('exam_date')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
        });

        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->decimal('marks_obtained', 6, 2)->nullable();
            $table->decimal('marks_total', 6, 2);
            $table->string('grade')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->unique(['tenant_id', 'exam_id', 'student_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exams');
    }
};
