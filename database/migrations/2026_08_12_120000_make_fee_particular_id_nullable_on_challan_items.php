<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ad-hoc challan items (Add Amount, Direct Payment) have no catalogued
     * FeeParticular to point at, so fee_particular_id must become nullable.
     * Uses raw SQL for the column-type change to avoid adding doctrine/dbal
     * as a dependency just for Blueprint::change().
     */
    public function up(): void
    {
        Schema::table('challan_items', function (Blueprint $table) {
            $table->dropForeign(['fee_particular_id']);
        });

        DB::statement('ALTER TABLE challan_items MODIFY fee_particular_id BIGINT UNSIGNED NULL');

        Schema::table('challan_items', function (Blueprint $table) {
            $table->foreign('fee_particular_id')->references('id')->on('fee_particulars')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('challan_items', function (Blueprint $table) {
            $table->dropForeign(['fee_particular_id']);
        });

        DB::statement('ALTER TABLE challan_items MODIFY fee_particular_id BIGINT UNSIGNED NOT NULL');

        Schema::table('challan_items', function (Blueprint $table) {
            $table->foreign('fee_particular_id')->references('id')->on('fee_particulars');
        });
    }
};
