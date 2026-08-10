<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->unsignedInteger('max_students')->nullable();
            $table->unsignedInteger('max_campuses')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->after('status');
            $table->date('trial_ends_at')->nullable()->after('plan_id');
            $table->enum('subscription_status', ['trial', 'active', 'past_due', 'cancelled'])->default('trial')->after('trial_ends_at');
            $table->string('primary_color', 7)->nullable()->after('logo_url');

            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
        });

        // Reference data needed immediately by tenant provisioning/the Super Admin
        // Portal's plan picker -- inserted here rather than a separate seeder step
        // so it's guaranteed present without a manual `db:seed` run.
        DB::table('plans')->insertOrIgnore([
            ['name' => 'Basic', 'slug' => 'basic', 'price' => 0, 'billing_cycle' => 'monthly', 'max_students' => 100, 'max_campuses' => 1, 'features' => json_encode(['academic', 'admissions', 'finance']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro', 'slug' => 'pro', 'price' => 49, 'billing_cycle' => 'monthly', 'max_students' => 1000, 'max_campuses' => 5, 'features' => json_encode(['academic', 'admissions', 'finance', 'exam', 'hrm', 'general']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'price' => 149, 'billing_cycle' => 'monthly', 'max_students' => null, 'max_campuses' => null, 'features' => json_encode(['academic', 'admissions', 'finance', 'exam', 'hrm', 'general']), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'trial_ends_at', 'subscription_status', 'primary_color']);
        });

        Schema::dropIfExists('plans');
    }
};
