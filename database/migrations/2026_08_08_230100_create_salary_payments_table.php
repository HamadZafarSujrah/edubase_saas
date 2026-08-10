<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('salary_plan_id');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('deductions_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('salary_plan_id')->references('id')->on('salary_plans')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['tenant_id', 'employee_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
