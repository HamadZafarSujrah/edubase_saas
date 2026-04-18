<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // 'module' corresponds to UI categories like "Student Management", "Fee Management", etc.
            $table->string('module')->index();
            // 'permission' corresponds to exact actions: 'student.add', 'fee.delete', 'report.pdf' 
            $table->string('permission')->index();
            // Optional boolean. True means granted, False means explicitly denied.
            $table->boolean('is_granted')->default(true);
            $table->timestamps();

            // Enforce that a user cannot have duplicate entries for exactly the same permission action
            $table->unique(['user_id', 'permission']);

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_permissions');
    }
}
