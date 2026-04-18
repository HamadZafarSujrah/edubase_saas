<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_plan_id')->nullable()->after('section_id');
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['fee_plan_id']);
            $table->dropColumn('fee_plan_id');
        });
    }
};
