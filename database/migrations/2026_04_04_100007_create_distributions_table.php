<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DISTRIBUTIONS / PENYALURAN BANTUAN
 *
 * Mencatat seluruh penyaluran dari dana yang terkumpul via campaign.
 * Satu campaign bisa punya banyak distribusi (mis. zakat maal
 * disalurkan ke 8 asnaf dalam beberapa batch).
 *
 * Desain kunci:
 * - `campaign_id` nullable: distribusi bisa dari kas umum, tidak harus
 *    terikat campaign tertentu.
 * - `recipient_type` + `recipient_name`: menggambarkan penerima manfaat
 *    tanpa harus membuat tabel penerima tersendiri. JSON `meta` bisa
 *    menyimpan data penerima lebih detail (NIK, alamat, dsb.)
 * - `distributed_amount` dan `distributed_unit` konsisten dengan
 *    goal_type di campaigns.
 * - `evidence_*`: dokumentasi bukti penyaluran untuk transparansi publik.
 * - `approved_by` + `distributed_by`: dua level otorisasi agar penyaluran
 *    teraudit dengan baik.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();

            // Source dana (dari campaign mana)
            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('campaigns')
                ->nullOnDelete();

            // Lembaga yang menyalurkan
            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            $table->string('distribution_code', 50)->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();

            // Kategori penerima manfaat (8 asnaf + umum)
            $table->enum('recipient_type', [
                'fakir',
                'miskin',
                'amil',
                'muallaf',
                'riqab',      // Hamba sahaya / tebusan
                'gharimin',   // Orang yang berhutang
                'fisabilillah',
                'ibnu_sabil',
                'general',    // Penerima umum non-zakat
                'institution', // Ke lembaga / yayasan
            ])->default('general')->index();

            // Data penerima (bisa individu atau lembaga)
            $table->string('recipient_name')->nullable();
            $table->unsignedInteger('recipient_count')->default(1); // jumlah penerima/KK

            // Nilai yang disalurkan
            $table->unsignedBigInteger('distributed_amount')->default(0); // Rupiah
            $table->unsignedInteger('distributed_unit')->default(0);      // unit barang
            $table->string('unit_label', 30)->nullable();

            // Bentuk penyaluran
            $table->enum('distribution_type', [
                'cash',         // Uang tunai
                'goods',        // Sembako, alat tulis, dll
                'service',      // Layanan kesehatan, beasiswa
                'mixed',        // Gabungan cash + barang
            ])->default('cash')->index();

            $table->enum('status', [
                'draft',
                'approved',
                'distributed',  // Sudah disalurkan
                'reported',     // Laporan lengkap sudah diupload
            ])->default('draft')->index();

            $table->date('distribution_date')->nullable()->index();

            // Lokasi penyaluran
            $table->string('location')->nullable();

            // Bukti penyaluran (foto, dokumen)
            $table->json('evidence_files')->nullable(); // array path foto/dokumen
            $table->text('notes')->nullable();

            // Detail penerima & kondisi (bisa simpan array data penerima)
            $table->json('meta')->nullable();

            // Alur otorisasi
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('distributed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['recipient_type', 'status']);
            $table->index(['distribution_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
