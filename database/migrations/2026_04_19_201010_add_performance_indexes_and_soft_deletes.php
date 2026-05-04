<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add Soft Deletes to existing tables
        if (Schema::hasTable('campuses')) {
            Schema::table('campuses', function (Blueprint $table) {
                if (!Schema::hasColumn('campuses', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // 2. Add missing columns
        if (Schema::hasTable('sections')) {
            Schema::table('sections', function (Blueprint $table) {
                if (!Schema::hasColumn('sections', 'campus_id')) {
                    $table->unsignedBigInteger('campus_id')->nullable()->after('school_class_id');
                    $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
                }
            });
        }

        // 3. Add Composite Performance Indexes (Safe Check)
        $this->addIndexIfNotExists('campuses', ['tenant_id', 'is_active'], 'idx_campuses_tenant_active');
        $this->addIndexIfNotExists('sessions', ['tenant_id', 'is_active'], 'idx_sessions_tenant_active');
        $this->addIndexIfNotExists('school_classes', ['tenant_id', 'is_active'], 'idx_classes_tenant_active');
        
        $this->addIndexIfNotExists('sections', ['tenant_id', 'school_class_id'], 'idx_sections_tenant_class');
        $this->addIndexIfNotExists('sections', ['tenant_id', 'campus_id'], 'idx_sections_tenant_campus');
        $this->addIndexIfNotExists('sections', ['tenant_id', 'is_active'], 'idx_sections_tenant_active');

        $this->addIndexIfNotExists('students', ['tenant_id', 'session_id'], 'idx_students_tenant_session');
        $this->addIndexIfNotExists('students', ['tenant_id', 'campus_id'], 'idx_students_tenant_campus');
        $this->addIndexIfNotExists('students', ['tenant_id', 'school_class_id'], 'idx_students_tenant_class');
        $this->addIndexIfNotExists('students', ['tenant_id', 'section_id'], 'idx_students_tenant_section');
        $this->addIndexIfNotExists('students', ['tenant_id', 'is_active'], 'idx_students_tenant_active');
        $this->addIndexIfNotExists('students', ['admission_no'], 'idx_students_admission_no');
    }

    /**
     * Helper to add index only if it doesn't exist
     */
    private function addIndexIfNotExists(string $table, array $columns, string $indexName)
    {
        if (!Schema::hasTable($table)) return;

        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $indexes = $conn->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);

        if (empty($indexes)) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Manual cleanup if needed, but since we use safe checks in up(), 
        // we should try to be clean here too.
        $this->dropIndexIfExists('students', 'idx_students_tenant_session');
        $this->dropIndexIfExists('students', 'idx_students_tenant_campus');
        $this->dropIndexIfExists('students', 'idx_students_tenant_class');
        $this->dropIndexIfExists('students', 'idx_students_tenant_section');
        $this->dropIndexIfExists('students', 'idx_students_tenant_active');
        $this->dropIndexIfExists('students', 'idx_students_admission_no');

        $this->dropIndexIfExists('sections', 'idx_sections_tenant_active');
        $this->dropIndexIfExists('sections', 'idx_sections_tenant_campus');
        $this->dropIndexIfExists('sections', 'idx_sections_tenant_class');
        
        if (Schema::hasTable('sections') && Schema::hasColumn('sections', 'campus_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropForeign(['campus_id']);
                $table->dropColumn('campus_id');
            });
        }

        $this->dropIndexIfExists('school_classes', 'idx_classes_tenant_active');
        $this->dropIndexIfExists('sessions', 'idx_sessions_tenant_active');
        $this->dropIndexIfExists('campuses', 'idx_campuses_tenant_active');

        if (Schema::hasTable('tenants') && Schema::hasColumn('tenants', 'deleted_at')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('campuses') && Schema::hasColumn('campuses', 'deleted_at')) {
            Schema::table('campuses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    private function dropIndexIfExists(string $table, string $indexName)
    {
        if (!Schema::hasTable($table)) return;

        $conn = Schema::getConnection();
        $indexes = $conn->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);

        if (!empty($indexes)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
