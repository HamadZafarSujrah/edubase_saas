<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('phone');
            $table->string('recipient_name')->nullable();
            $table->text('message');
            $table->enum('type', ['fee', 'attendance', 'result', 'blast']);
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->string('gateway')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->index(['tenant_id', 'type', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
