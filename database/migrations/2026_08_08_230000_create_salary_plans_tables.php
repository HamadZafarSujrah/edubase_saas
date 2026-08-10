<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 12, 2);
            $table->enum('frequency', ['monthly'])->default('monthly');
            $table->date('effective_from');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'employee_id', 'status']);
        });

        Schema::create('salary_allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('salary_plan_id');
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['allowance', 'deduction']);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('salary_plan_id')->references('id')->on('salary_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_allowances');
        Schema::dropIfExists('salary_plans');
    }
};
