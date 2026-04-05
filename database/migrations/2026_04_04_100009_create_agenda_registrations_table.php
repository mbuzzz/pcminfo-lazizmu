<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AGENDA_REGISTRATIONS / PESERTA KEGIATAN
 *
 * Tabel pendukung agendas: daftar peserta yang mendaptar ke kegiatan
 * yang memerlukan registrasi (requires_registration = true).
 * Dipisah dari agendas agar bisa di-paginate dan diekspor sendiri.
 * `user_id` nullable untuk pendaftaran dari publik (non-login).
 * `meta` menyimpan data form dinamis (ukuran baju untuk seragam,
 * asal instansi, dll.) tanpa perlu alter tabel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agenda_id')
                ->constrained('agendas')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot data pendaftar
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('institution_name')->nullable(); // asal instansi/lembaga

            $table->string('registration_code', 30)->unique()->index();

            $table->enum('status', [
                'pending',    // Menunggu konfirmasi panitia
                'confirmed',  // Sudah dikonfirmasi
                'attended',   // Hadir di acara
                'cancelled',  // Membatalkan pendaftaran
                'rejected',   // Ditolak (kuota penuh dll)
            ])->default('pending')->index();

            $table->text('notes')->nullable();     // Catatan dari pendaftar
            $table->text('admin_notes')->nullable(); // Catatan dari panitia

            // Data form dinamis (ukuran baju, kebutuhan khusus, dll)
            $table->json('meta')->nullable();

            $table->timestamp('checked_in_at')->nullable();

            $table->timestamps();

            $table->index(['agenda_id', 'status']);
            $table->unique(['agenda_id', 'email']); // satu email = satu registrasi per event
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_registrations');
    }
};
