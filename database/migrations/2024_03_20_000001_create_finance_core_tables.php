<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceCoreTables extends Migration
{
    public function up()
    {
        // 1. General Ledger Accounts (Chart of Accounts)
        Schema::create('gl_accounts', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('tenant_id');
            $col->string('code', 20); // e.g. 1001
            $col->string('name'); // e.g. Petty Cash
            $col->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $col->unsignedBigInteger('parent_id')->nullable();
            $col->boolean('is_active')->default(true);
            $col->timestamps();

            $col->index(['tenant_id', 'code']);
        });

        // 2. Journal Entry (Voucher Header)
        Schema::create('journal_entries', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('tenant_id');
            $col->unsignedBigInteger('campus_id')->nullable();
            $col->date('transaction_date');
            $col->string('voucher_no')->unique(); // e.g. JV-2026-0001
            $col->string('reference')->nullable();
            $col->text('narration')->nullable();
            $col->unsignedBigInteger('created_by')->nullable();
            $col->timestamps();
        });

        // 3. Journal Items (Debt/Credit Lines)
        Schema::create('journal_items', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('journal_entry_id');
            $col->unsignedBigInteger('gl_account_id');
            $col->decimal('debit', 15, 2)->default(0);
            $col->decimal('credit', 15, 2)->default(0);
            $col->string('item_memo')->nullable();
            $col->timestamps();

            $col->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $col->foreign('gl_account_id')->references('id')->on('gl_accounts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('gl_accounts');
    }
}
