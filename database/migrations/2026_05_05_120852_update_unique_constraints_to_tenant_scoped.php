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
            // admission_no was created unique
            $table->dropUnique('students_admission_no_unique');
            
            // Add new composite unique constraints (tenant-scoped)
            $table->unique(['tenant_id', 'email']);
            $table->unique(['tenant_id', 'admission_no']);
        });

        Schema::table('families', function (Blueprint $table) {
            $table->dropUnique(['email']);
            // family_no was created unique in 2026_04_19_202815_create_admission_extensions_tables.php
            $table->dropUnique('families_family_no_unique');
            
            $table->unique(['tenant_id', 'email']);
            $table->unique(['tenant_id', 'family_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'email']);
            $table->dropUnique(['tenant_id', 'admission_no']);
            
            $table->unique('email');
            $table->unique('admission_no');
        });

        Schema::table('families', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'email']);
            $table->dropUnique(['tenant_id', 'family_no']);
            
            $table->unique('email');
            $table->unique('family_no');
        });
    }
};
