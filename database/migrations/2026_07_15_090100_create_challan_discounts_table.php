<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('challan_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challan_id')->constrained('challans')->onDelete('cascade');
            $table->foreignId('discount_type_id')->nullable()->constrained('discount_types')->nullOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_discounts');
    }
};
