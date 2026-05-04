<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('paid_amount');
            $table->unsignedBigInteger('receiving_account_id')->nullable()->after('discount_amount');
            $table->unsignedBigInteger('discount_account_id')->nullable()->after('receiving_account_id');
            $table->date('paid_date')->nullable()->after('discount_account_id');
            $table->string('receipt_no')->nullable()->after('paid_date');
            $table->text('challan_notes')->nullable()->after('receipt_no');
            $table->unsignedBigInteger('paid_by')->nullable()->after('challan_notes');
        });
    }

    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn([
                'paid_amount', 'discount_amount', 'receiving_account_id',
                'discount_account_id', 'paid_date', 'receipt_no', 'challan_notes', 'paid_by'
            ]);
        });
    }
};
