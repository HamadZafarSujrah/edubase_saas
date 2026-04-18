<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeBaseTables extends Migration
{
    public function up()
    {
        // 1. Fee Particulars (The 'Heads' like Tuition Fee, Admission Fee)
        Schema::create('fee_particulars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 2. Fee Plans (The 'Packages' like Matric Plan, Nursery Plan)
        Schema::create('fee_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // 3. The Master Configuration (Amounts & Months)
        Schema::create('fee_plan_particulars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('fee_plan_id');
            $table->unsignedBigInteger('fee_particular_id');

            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->boolean('is_first_time')->default(false); // Only for new admissions

            // Repeating Months (Jan - Dec)
            $table->boolean('jan')->default(false);
            $table->boolean('feb')->default(false);
            $table->boolean('mar')->default(false);
            $table->boolean('apr')->default(false);
            $table->boolean('may')->default(false);
            $table->boolean('jun')->default(false);
            $table->boolean('jul')->default(false);
            $table->boolean('aug')->default(false);
            $table->boolean('sep')->default(false);
            $table->boolean('oct')->default(false);
            $table->boolean('nov')->default(false);
            $table->boolean('dec')->default(false);

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses');
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans');
            $table->foreign('fee_particular_id')->references('id')->on('fee_particulars');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fee_plan_particulars');
        Schema::dropIfExists('fee_plans');
        Schema::dropIfExists('fee_particulars');
    }
}
