<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserModulePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('user_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('module_key'); // e.g. 'admission_create', 'finance_gl_view'
            $table->boolean('is_allowed')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'module_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_module_permissions');
    }
}
