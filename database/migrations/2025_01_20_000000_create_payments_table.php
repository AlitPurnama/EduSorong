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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('midtrans_order_id')->unique()->nullable(); // Order ID dari Midtrans
            $table->string('midtrans_transaction_id')->nullable(); // Transaction ID dari Midtrans
            $table->string('payment_method'); // qris, ewallet, virtual_account
            $table->string('payment_channel')->nullable(); // ovo, dana, linkaja, bca
            $table->string('status')->default('pending'); // pending, paid, expired, failed, cancel
            $table->string('transaction_status')->nullable(); // settlement, pending, deny, cancel, expire
            $table->unsignedBigInteger('amount');
            $table->string('currency')->default('IDR');
            $table->text('reference_id')->nullable(); // Reference ID untuk tracking
            $table->text('qr_string')->nullable(); // QR code string untuk QRIS
            $table->text('qr_url')->nullable(); // QR code image URL
            $table->string('virtual_account_number')->nullable(); // Nomor VA untuk BCA
            $table->text('deeplink_url')->nullable(); // Deep link untuk E-Wallet
            $table->text('notification_data')->nullable(); // JSON data dari webhook
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

