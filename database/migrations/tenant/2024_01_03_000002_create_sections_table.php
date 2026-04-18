<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('school_class_id');
            
            $table->string('name'); // "A", "B", "Rose"
            $table->string('room_number')->nullable();
            $table->integer('capacity')->default(30); 
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            $table->foreign('tenant_id')
                  ->references('id')->on('tenants')
                  ->onDelete('cascade');
                  
            $table->foreign('school_class_id')
                  ->references('id')->on('school_classes')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
