<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['present', 'absent', 'leave', 'late']);
            $table->enum('method', ['manual', 'qr', 'biometric'])->default('manual');
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['tenant_id', 'employee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
    }
};
