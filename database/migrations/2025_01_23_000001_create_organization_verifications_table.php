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
        Schema::create('organization_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('organization_name');
            $table->text('organization_description')->nullable();
            $table->string('npwp')->nullable(); // Nomor NPWP yayasan
            $table->string('phone')->nullable(); // Nomor telepon yayasan
            $table->string('website')->nullable(); // Website yayasan (opsional)
            $table->text('address')->nullable(); // Alamat yayasan
            $table->string('document_path')->nullable(); // Path ke dokumen verifikasi (AKTA, SIUP, dll)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_verifications');
    }
};

