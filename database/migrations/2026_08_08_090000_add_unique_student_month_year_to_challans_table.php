<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enforces at the DB level the invariant FeeService::generateMonthlyChallan()
     * already tries to enforce with a check-then-insert (which races under
     * concurrent requests): one challan per student per month/year.
     */
    public function up(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->unique(['tenant_id', 'student_id', 'month', 'year'], 'challans_tenant_student_month_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropUnique('challans_tenant_student_month_year_unique');
        });
    }
};
