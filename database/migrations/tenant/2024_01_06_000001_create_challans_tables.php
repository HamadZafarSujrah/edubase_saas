<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Challans (Header)
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->string('challan_no')->unique();
            $table->string('month'); // e.g., 'jan'
            $table->integer('year'); 
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('payable_after_due', 15, 2)->nullable();
            $table->string('status')->default('pending'); // pending, paid, partially_paid, void
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });

        // 2. Challan Items (Details)
        Schema::create('challan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->unsignedBigInteger('fee_particular_id');
            $table->string('particular_name');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->foreign('challan_id')->references('id')->on('challans')->onDelete('cascade');
            $table->foreign('fee_particular_id')->references('id')->on('fee_particulars');
        });
    }

    public function down()
    {
        Schema::dropIfExists('challan_items');
        Schema::dropIfExists('challans');
    }
};
