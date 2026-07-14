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
            $table->index('session_id');
            $table->index('campus_id');
            $table->index('school_class_id');
            $table->index('section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['campus_id']);
            $table->dropIndex(['school_class_id']);
            $table->dropIndex(['section_id']);
        });
    }
};
