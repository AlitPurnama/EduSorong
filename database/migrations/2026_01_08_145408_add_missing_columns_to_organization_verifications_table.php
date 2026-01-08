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
        Schema::table('organization_verifications', function (Blueprint $table) {
            // Add phone if not exists
            if (!Schema::hasColumn('organization_verifications', 'phone')) {
                $table->string('phone')->nullable()->after('npwp');
            }
            
            // Add website if not exists
            if (!Schema::hasColumn('organization_verifications', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            
            // Add address if not exists
            if (!Schema::hasColumn('organization_verifications', 'address')) {
                $table->text('address')->nullable()->after('website');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('organization_verifications', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('organization_verifications', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('organization_verifications', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
