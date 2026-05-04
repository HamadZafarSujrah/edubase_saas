<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateGlGroupsEnumValues extends Migration
{
    public function up()
    {
        // 1. Temporarily change to VARCHAR to avoid ENUM truncation errors during data update
        DB::statement("ALTER TABLE gl_groups MODIFY COLUMN account_class VARCHAR(255)");

        // 2. Standardize data to lowercase singular
        DB::table('gl_groups')->where('account_class', 'Assets')->update(['account_class' => 'asset']);
        DB::table('gl_groups')->where('account_class', 'Liabilities')->update(['account_class' => 'liability']);
        DB::table('gl_groups')->where('account_class', 'Equity')->update(['account_class' => 'equity']);
        DB::table('gl_groups')->where('account_class', 'Income')->update(['account_class' => 'income']);
        DB::table('gl_groups')->where('account_class', 'Expense')->update(['account_class' => 'expense']);

        // 3. Change to new ENUM definition
        DB::statement("ALTER TABLE gl_groups MODIFY COLUMN account_class ENUM('asset', 'liability', 'equity', 'income', 'expense')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE gl_groups MODIFY COLUMN account_class VARCHAR(255)");
        DB::table('gl_groups')->where('account_class', 'asset')->update(['account_class' => 'Assets']);
        DB::table('gl_groups')->where('account_class', 'liability')->update(['account_class' => 'Liabilities']);
        DB::table('gl_groups')->where('account_class', 'equity')->update(['account_class' => 'Equity']);
        DB::table('gl_groups')->where('account_class', 'income')->update(['account_class' => 'Income']);
        DB::table('gl_groups')->where('account_class', 'expense')->update(['account_class' => 'Expense']);
        DB::statement("ALTER TABLE gl_groups MODIFY COLUMN account_class ENUM('Assets', 'Liabilities', 'Equity', 'Income', 'Expense')");
    }
}
