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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('donor_name')->nullable()->after('user_id');
            $table->string('donor_phone')->nullable()->after('donor_name');
            $table->string('donor_email')->nullable()->after('donor_phone');
            $table->text('donor_message')->nullable()->after('donor_email');
            $table->boolean('is_anonymous')->default(false)->after('donor_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['donor_name', 'donor_phone', 'donor_email', 'donor_message', 'is_anonymous']);
        });
    }
};

