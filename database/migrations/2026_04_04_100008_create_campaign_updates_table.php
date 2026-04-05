<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CAMPAIGN_UPDATES / LOG PERKEMBANGAN CAMPAIGN
 *
 * Tabel pendukung campaigns: menyimpan update berkala dari panitia
 * kepada donatur (mis. "Alhamdulillah dana terkumpul 80%", laporan
 * foto perkembangan, milestone yang tercapai).
 * Dipisah dari tabel utama agar campaign bisa punya log update
 * tanpa memperbesar row utama dan mudah di-paginate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete(); // hapus update jika campaign dihapus

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('content');
            $table->json('media_files')->nullable(); // array foto/video update

            $table->enum('type', [
                'progress',   // Laporan perkembangan
                'milestone',  // Pencapaian target tertentu
                'final',      // Laporan akhir penyaluran
                'general',
            ])->default('progress')->index();

            // Snapshot progress saat update ditulis
            $table->unsignedBigInteger('amount_at_update')->nullable();
            $table->unsignedInteger('unit_at_update')->nullable();
            $table->unsignedInteger('donor_count_at_update')->nullable();

            $table->boolean('notify_donors')->default(false); // kirim notif ke donatur
            $table->timestamp('published_at')->nullable()->index();

            $table->timestamps();

            $table->index(['campaign_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_updates');
    }
};
