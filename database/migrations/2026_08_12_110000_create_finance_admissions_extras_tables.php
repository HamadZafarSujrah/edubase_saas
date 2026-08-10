<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Installments split a single month's bill into several challans, so the
        // existing (tenant_id, student_id, month, year) unique index -- one bill
        // per student per month -- must widen to allow N rows for that same
        // student/month/year, distinguished by installment_no.
        Schema::table('challans', function (Blueprint $table) {
            $table->unsignedTinyInteger('installment_no')->default(1)->after('year');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropUnique('challans_tenant_student_month_year_unique');
            $table->unique(
                ['tenant_id', 'student_id', 'month', 'year', 'installment_no'],
                'challans_tenant_student_month_year_installment_unique'
            );
        });

        Schema::create('challan_void_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('challan_id');
            $table->string('challan_no');
            $table->unsignedBigInteger('student_id');
            $table->string('student_name');
            $table->decimal('voided_amount', 15, 2);
            $table->string('void_reason')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('challan_id')->references('id')->on('challans')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('voided_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challan_void_logs');

        Schema::table('challans', function (Blueprint $table) {
            $table->dropUnique('challans_tenant_student_month_year_installment_unique');
            $table->unique(['tenant_id', 'student_id', 'month', 'year'], 'challans_tenant_student_month_year_unique');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn('installment_no');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
