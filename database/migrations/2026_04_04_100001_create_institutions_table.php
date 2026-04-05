<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * INSTITUTIONS / AMAL USAHA
 *
 * Tabel untuk seluruh lembaga/amal usaha di bawah naungan PCM Genteng,
 * termasuk sekolah, klinik, BUMM, Lazismu cabang, dsb.
 * Field `type` mengelompokkan jenis lembaga agar bisa difilter per kategori.
 * `meta` JSON menyimpan data tambahan yang sifatnya per-type sehingga schema
 * tetap ramping tanpa banyak nullable column.
 * `is_featured` digunakan untuk menampilkan lembaga unggulan di homepage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->string('acronym', 20)->nullable();     // mis. SMPM, SDIM
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            $table->enum('type', [
                'school',         // Sekolah (SD, SMP, SMA)
                'kindergarten',   // TK / PAUD
                'clinic',         // Klinik / RSIA
                'mosque',         // Masjid / Mushola binaan
                'finance',        // Lazismu, BMT
                'enterprise',     // BUMM dan unit usaha
                'social',         // Panti, LPA, dll
                'other',
            ])->default('other')->index();

            $table->enum('status', [
                'active',
                'inactive',
                'development',    // Sedang dalam pembangunan/rintisan
            ])->default('active')->index();

            // Kontak & Lokasi
            $table->string('address')->nullable();
            $table->string('village')->nullable();       // kelurahan/desa
            $table->string('district')->nullable();      // kecamatan
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Media
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            // Tahun berdiri dan akreditasi
            $table->year('founded_year')->nullable();
            $table->string('accreditation', 5)->nullable();  // A, B, C

            // Data fleksibel per type (jumlah siswa, kapasitas tempat tidur, dll)
            $table->json('meta')->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['city', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
