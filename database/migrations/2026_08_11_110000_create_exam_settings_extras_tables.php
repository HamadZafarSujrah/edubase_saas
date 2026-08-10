<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('academic_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('type', ['holiday', 'event'])->default('holiday');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Named "class_shifts" (not "shifts") -- that table name is already
        // taken by the HRM employee-shift feature.
        Schema::create('class_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('class_shift_id')->nullable()->after('numeric_value');
            $table->foreign('class_shift_id')->references('id')->on('class_shifts')->onDelete('set null');
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('category')->default('general');
            $table->text('body');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['class_shift_id']);
            $table->dropColumn('class_shift_id');
        });
        Schema::dropIfExists('class_shifts');
        Schema::dropIfExists('academic_dates');
        Schema::dropIfExists('standings');
    }
};
