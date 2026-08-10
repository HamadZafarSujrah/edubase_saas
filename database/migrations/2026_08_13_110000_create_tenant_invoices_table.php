<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Not tenant-scoped (no HasTenant) -- this is platform-level billing
        // data the Super Admin Portal must see across every tenant, which
        // TenantScope's fail-closed-with-no-session-tenant behavior would
        // otherwise hide entirely. The tenant-side "My Subscription" view
        // filters explicitly by tenant_id instead of relying on auto-scoping.
        Schema::create('tenant_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('invoice_no')->unique();
            $table->decimal('amount', 10, 2);
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'paid', 'void'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['tenant_id', 'billing_period_start', 'billing_period_end'], 'tenant_invoices_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_invoices');
    }
};
