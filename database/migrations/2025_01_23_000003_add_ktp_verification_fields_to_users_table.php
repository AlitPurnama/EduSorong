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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ktp_number')->nullable()->after('ktp_verified');
            $table->string('ktp_name')->nullable()->after('ktp_number');
            $table->string('ktp_photo')->nullable()->after('ktp_name');
            $table->enum('ktp_verification_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('ktp_photo');
            $table->text('ktp_rejection_reason')->nullable()->after('ktp_verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_number',
                'ktp_name',
                'ktp_photo',
                'ktp_verification_status',
                'ktp_rejection_reason',
            ]);
        });
    }
};

