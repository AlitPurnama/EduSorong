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
        // Check if table exists and add missing columns
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                // Add midtrans_order_id if it doesn't exist
                if (!Schema::hasColumn('payments', 'midtrans_order_id')) {
                    $table->string('midtrans_order_id')->unique()->nullable()->after('user_id');
                }
                
                // Add midtrans_transaction_id if it doesn't exist
                if (!Schema::hasColumn('payments', 'midtrans_transaction_id')) {
                    $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
                }
                
                // Add payment_method if it doesn't exist
                if (!Schema::hasColumn('payments', 'payment_method')) {
                    $table->string('payment_method')->after('midtrans_transaction_id');
                }
                
                // Add payment_channel if it doesn't exist
                if (!Schema::hasColumn('payments', 'payment_channel')) {
                    $table->string('payment_channel')->nullable()->after('payment_method');
                }
                
                // Add status if it doesn't exist
                if (!Schema::hasColumn('payments', 'status')) {
                    $table->string('status')->default('pending')->after('payment_channel');
                }
                
                // Add transaction_status if it doesn't exist
                if (!Schema::hasColumn('payments', 'transaction_status')) {
                    $table->string('transaction_status')->nullable()->after('status');
                }
                
                // Add currency if it doesn't exist
                if (!Schema::hasColumn('payments', 'currency')) {
                    $table->string('currency')->default('IDR')->after('amount');
                }
                
                // Add reference_id if it doesn't exist
                if (!Schema::hasColumn('payments', 'reference_id')) {
                    $table->text('reference_id')->nullable()->after('currency');
                }
                
                // Add qr_string if it doesn't exist
                if (!Schema::hasColumn('payments', 'qr_string')) {
                    $table->text('qr_string')->nullable()->after('reference_id');
                }
                
                // Add qr_url if it doesn't exist
                if (!Schema::hasColumn('payments', 'qr_url')) {
                    $table->text('qr_url')->nullable()->after('qr_string');
                }
                
                // Add virtual_account_number if it doesn't exist
                if (!Schema::hasColumn('payments', 'virtual_account_number')) {
                    $table->string('virtual_account_number')->nullable()->after('qr_url');
                }
                
                // Add deeplink_url if it doesn't exist
                if (!Schema::hasColumn('payments', 'deeplink_url')) {
                    $table->text('deeplink_url')->nullable()->after('virtual_account_number');
                }
                
                // Add notification_data if it doesn't exist
                if (!Schema::hasColumn('payments', 'notification_data')) {
                    $table->text('notification_data')->nullable()->after('deeplink_url');
                }
                
                // Add expires_at if it doesn't exist
                if (!Schema::hasColumn('payments', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('notification_data');
                }
                
                // Add paid_at if it doesn't exist
                if (!Schema::hasColumn('payments', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('expires_at');
                }
                
                // Add guest donation fields if they don't exist
                if (!Schema::hasColumn('payments', 'donor_name')) {
                    $table->string('donor_name')->nullable()->after('user_id');
                }
                
                if (!Schema::hasColumn('payments', 'donor_phone')) {
                    $table->string('donor_phone')->nullable()->after('donor_name');
                }
                
                if (!Schema::hasColumn('payments', 'donor_email')) {
                    $table->string('donor_email')->nullable()->after('donor_phone');
                }
                
                if (!Schema::hasColumn('payments', 'donor_message')) {
                    $table->text('donor_message')->nullable()->after('donor_email');
                }
                
                if (!Schema::hasColumn('payments', 'is_anonymous')) {
                    $table->boolean('is_anonymous')->default(false)->after('donor_message');
                }
            });
        } else {
            // If table doesn't exist, create it
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('donor_name')->nullable()->after('user_id');
                $table->string('donor_phone')->nullable()->after('donor_name');
                $table->string('donor_email')->nullable()->after('donor_phone');
                $table->text('donor_message')->nullable()->after('donor_email');
                $table->boolean('is_anonymous')->default(false)->after('donor_message');
                $table->string('midtrans_order_id')->unique()->nullable();
                $table->string('midtrans_transaction_id')->nullable();
                $table->string('payment_method');
                $table->string('payment_channel')->nullable();
                $table->string('status')->default('pending');
                $table->string('transaction_status')->nullable();
                $table->unsignedBigInteger('amount');
                $table->string('currency')->default('IDR');
                $table->text('reference_id')->nullable();
                $table->text('qr_string')->nullable();
                $table->text('qr_url')->nullable();
                $table->string('virtual_account_number')->nullable();
                $table->text('deeplink_url')->nullable();
                $table->text('notification_data')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is additive, so we don't drop columns in down()
        // If you need to rollback, create a separate migration
    }
};

