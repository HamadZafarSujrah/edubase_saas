<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allowance_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->enum('type', ['allowance', 'deduction'])->default('allowance');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('employee_standings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('employee_custom_field_defs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('label');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'label']);
        });

        Schema::table('employee_attendance', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('marked_by');
            $table->unsignedBigInteger('locked_by')->nullable()->after('is_locked');
            $table->timestamp('locked_at')->nullable()->after('locked_by');

            $table->foreign('locked_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('employee_attendance', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['is_locked', 'locked_by', 'locked_at']);
        });

        Schema::dropIfExists('employee_custom_field_defs');
        Schema::dropIfExists('employee_standings');
        Schema::dropIfExists('allowance_types');
    }
};
