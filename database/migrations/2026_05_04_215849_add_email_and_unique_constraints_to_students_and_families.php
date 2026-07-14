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
        // Add email to students
        if (!Schema::hasColumn('students', 'email')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('email')->nullable()->unique()->after('last_name');
            });
        }

        // Add email to families
        if (!Schema::hasColumn('families', 'email')) {
            Schema::table('families', function (Blueprint $table) {
                $table->string('email')->nullable()->unique()->after('mother_cnic');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
