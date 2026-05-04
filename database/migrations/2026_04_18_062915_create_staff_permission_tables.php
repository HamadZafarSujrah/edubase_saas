<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffPermissionTables extends Migration
{
    public function up()
    {
        // 1. Campus Access Table
        Schema::create('user_campus_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campus_id');
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. GL Account Access Table (Cashier Privileges)
        Schema::create('user_account_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('account_id');
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. User Preferences Support
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('power_level');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_campus_permissions');
        Schema::dropIfExists('user_account_permissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('preferences');
        });
    }
}
