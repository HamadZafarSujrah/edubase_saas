<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToGlAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('parent_id');
            // Adding help comment: Links this Cash-In-Hand account to a specific employee/cashier
        });
    }

    public function down()
    {
        Schema::table('gl_accounts', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
