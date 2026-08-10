<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'leave', 'late']);
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // One attendance record per student per day.
            $table->unique(['tenant_id', 'student_id', 'date']);
            $table->index(['tenant_id', 'section_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};
