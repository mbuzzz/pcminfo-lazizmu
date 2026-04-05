<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AGENDAS / KEGIATAN
 *
 * Jadwal kegiatan PCM, PCW, majelis, dan lembaga di bawah naungan PCM.
 * `is_recurring` + `recurrence_rule` memungkinkan kegiatan rutin
 * (kajian Ahad pagi, pengajian bulanan) tanpa duplikasi baris.
 * `recurrence_rule` menggunakan format iCal RRULE untuk kompatibilitas
 * dengan library kalender.
 * `max_participants` NULL berarti tidak ada batas kuota.
 * `requires_registration` mengontrol apakah perlu form daftar peserta.
 * `institution_id` opsional — kegiatan bisa milik PCM pusat atau lembaga.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();

            $table->enum('type', [
                'kajian',         // Pengajian / Kajian
                'meeting',        // Rapat / Musyawarah
                'social',         // Kegiatan sosial / baksos
                'education',      // Seminar / Pelatihan
                'competition',    // Lomba / Olimpiade
                'commemoration',  // Peringatan hari besar Islam
                'other',
            ])->default('other')->index();

            $table->enum('status', [
                'draft',
                'published',
                'cancelled',
                'completed',
            ])->default('draft')->index();

            // Waktu kegiatan
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->nullable()->index();

            // Lokasi
            $table->string('location_name')->nullable();   // "Aula PCM Genteng"
            $table->string('location_address')->nullable();
            $table->string('maps_url')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('meeting_url')->nullable();     // Zoom/Meet link

            // Registrasi peserta
            $table->boolean('requires_registration')->default(false);
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedInteger('registered_count')->default(0); // denormalized

            // Kegiatan berulang
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable(); // iCal RRULE format

            // Kontak panitia
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'start_at']);
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
