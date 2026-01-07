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
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            // Track if evidence is required and uploaded
            $table->boolean('requires_evidence')->default(true)->after('status');
            $table->boolean('evidence_uploaded')->default(false)->after('requires_evidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['requires_evidence', 'evidence_uploaded']);
        });
    }
};

