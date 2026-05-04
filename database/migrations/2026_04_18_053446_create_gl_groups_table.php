<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('gl_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->enum('account_class', ['Assets', 'Liabilities', 'Equity', 'Income', 'Expense']);
            $table->boolean('is_inactive')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'account_class']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('gl_groups');
    }
}
