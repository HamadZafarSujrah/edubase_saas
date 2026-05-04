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
        Schema::table('students', function (Blueprint $table) {
            $table->string('fee_plan_effect_from')->nullable()->after('fee_plan_id');
            $table->decimal('fee_plan_increment', 5, 2)->nullable()->after('fee_plan_effect_from');
            $table->string('fee_plan_year')->nullable()->after('fee_plan_increment');
            $table->string('fee_plan_discount_type')->nullable()->after('fee_plan_year');
            $table->text('fee_plan_notes')->nullable()->after('fee_plan_discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'fee_plan_effect_from',
                'fee_plan_increment',
                'fee_plan_year',
                'fee_plan_discount_type',
                'fee_plan_notes'
            ]);
        });
    }
};
