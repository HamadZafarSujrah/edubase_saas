<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FinalizeGlAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('gl_accounts', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('tenant_id');
            }
            if (!Schema::hasColumn('gl_accounts', 'is_inactive')) {
                $table->boolean('is_inactive')->default(false)->after('name');
            }
            if (!Schema::hasColumn('gl_accounts', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('gl_accounts', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropColumn(['group_id', 'is_inactive', 'created_by', 'updated_by']);
        });
    }
}
