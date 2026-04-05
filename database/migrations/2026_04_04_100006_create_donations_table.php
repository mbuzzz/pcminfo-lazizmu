<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DONATIONS / DATA DONASI
 *
 * Setiap transaksi donasi masuk dicatat di tabel ini.
 * Donasi bisa melalui campaign tertentu (via campaign_id) atau langsung
 * tanpa campaign (mis. donasi infaq umum, zakat fitrah one-off).
 *
 * Desain kunci:
 * - `amount` dan `unit_quantity` koeksistensi mendukung goal_type campaign.
 * - `payment_method` + `payment_channel`: cukup fleksibel untuk tunai,
 *    transfer bank, QRIS, OVO, Dana, dll.
 * - `payment_status`: terpisah dari status donasi agar bisa track settlement.
 * - `meta` JSON menyimpan payload dari payment gateway (Midtrans, Xendit,
 *    Tripay) tanpa perlu alter tabel setiap ada provider baru.
 * - `donor_*` disimpan snapshot agar data tidak berubah jika user edit profil.
 * - `is_anonymous`: jika true, semua `donor_*` disembunyikan di public.
 * - `transaction_code` diindex utk pencarian manual oleh admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('donor_id')->nullable()->index();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('transaction_code', 50)->unique()->index();
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_phone', 20)->nullable();
            $table->boolean('is_anonymous')->default(false)->index();

            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit_label', 30)->nullable();
            $table->text('message')->nullable();
            $table->string('payment_method', 50)->default('manual_transfer')->index();
            $table->string('payment_channel', 50)->nullable()->index();

            $table->string('status', 30)->default('pending')->index();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamp('verified_at')->nullable()->index();
            $table->timestamp('rejected_at')->nullable()->index();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['campaign_id', 'status', 'verified_at']);
            $table->index(['payer_email', 'status']);
            $table->index(['donor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
