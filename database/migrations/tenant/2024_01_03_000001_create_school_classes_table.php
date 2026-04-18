<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolClassesTable extends Migration
{
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            
            $table->string('name'); // e.g. "Grade 10" or "Playgroup"
            $table->integer('numeric_value')->nullable(); // useful for sorting: 1, 2, 3... 10
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            $table->foreign('tenant_id')
                  ->references('id')->on('tenants')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('school_classes');
    }
}
