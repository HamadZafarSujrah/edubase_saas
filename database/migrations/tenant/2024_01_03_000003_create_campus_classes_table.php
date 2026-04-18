<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampusClassesTable extends Migration
{
    public function up()
    {
        Schema::create('campus_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index(); // Security barrier
            
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('school_class_id');
            
            $table->timestamps();

            // Foreign Key Restraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            
            // A class can only be assigned to a campus once
            $table->unique(['campus_id', 'school_class_id']); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('campus_classes');
    }
}
