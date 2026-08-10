<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The pre-existing "house" column was a free-text string, unused anywhere
     * in the app (StudentAdmission's only reads/writes of it were removed
     * this round in favor of the new house_id FK). Dropping it because its
     * name collides with the new Student::house() relation -- Eloquent's
     * attribute lookup on a real column always wins over a same-named
     * relationship method, so $student->house would otherwise silently
     * return this dead string instead of the House model.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('house');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('house')->nullable();
        });
    }
};
