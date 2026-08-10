<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('motto')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('house_id')->nullable()->after('section_id');
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('set null');
        });

        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('section_id');
            $table->date('date');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'section_id', 'date']);
        });

        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->string('complainant_name')->nullable();
            $table->string('complainant_phone')->nullable();
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('ptm_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->unsignedBigInteger('school_class_id')->nullable();
            $table->string('title');
            $table->date('meeting_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ptm_schedules');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('diary_entries');
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->dropColumn('house_id');
        });
        Schema::dropIfExists('houses');
        Schema::dropIfExists('notices');
    }
};
