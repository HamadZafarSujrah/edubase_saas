<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * is_mapped lets FeePlanMapping::toggleMapping() unmap a particular without
     * hard-deleting the row (and every amount/min_amount/month/is_first_time
     * value configured on it). The unique index makes the check-then-create in
     * toggleMapping() safe against a double-click/slow-network race that would
     * otherwise insert two rows for the same mapping.
     */
    public function up(): void
    {
        Schema::table('fee_plan_particulars', function (Blueprint $table) {
            $table->boolean('is_mapped')->default(true)->after('is_first_time');
            $table->unique(
                ['tenant_id', 'campus_id', 'fee_plan_id', 'fee_particular_id'],
                'fpp_tenant_campus_plan_particular_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('fee_plan_particulars', function (Blueprint $table) {
            $table->dropUnique('fpp_tenant_campus_plan_particular_unique');
            $table->dropColumn('is_mapped');
        });
    }
};
