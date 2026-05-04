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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('student_prefix')->nullable()->after('code');
            $table->integer('next_student_number')->default(1)->after('student_prefix');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('system_id')->nullable()->unique()->after('admission_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('system_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['student_prefix', 'next_student_number']);
        });
    }
};
