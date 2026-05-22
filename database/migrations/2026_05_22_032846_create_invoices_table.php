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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key yang mengikat ke tabel subscriptions
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            
            // Kolom data invoice
            $table->string('invoice_number')->unique(); // Contoh: INV-20260522-0001
            $table->integer('amount');
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, expired
            $table->dateTime('paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
