<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeUsersTableForPrivileges extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('User')->after('status');
            }
            if (!Schema::hasColumn('users', 'power_level')) {
                $table->string('power_level')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'contact')) {
                $table->string('contact')->nullable()->after('power_level');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'status', 'role', 'power_level', 'contact', 'created_by', 'updated_by']);
        });
    }
}
