<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ClassManager::saveSection() had no uniqueness check, so a double-click
     * (or just re-adding the same letter by mistake) silently created two
     * "Section B" rows under the same class. The unique index makes that
     * impossible at the DB level; app-level validation in saveSection() now
     * gives a friendly error before ever reaching this constraint.
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->unique(
                ['tenant_id', 'school_class_id', 'name'],
                'sections_tenant_class_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropUnique('sections_tenant_class_name_unique');
        });
    }
};
