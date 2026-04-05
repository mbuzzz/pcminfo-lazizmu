<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CAMPAIGNS / PROGRAM PENGGALANGAN
 *
 * Inti dari modul Lazismu: campaign donasi, program sosial, dan
 * wakaf. Dibuat config-driven via JSON sehingga satu tabel bisa
 * menangani berbagai jenis program tanpa perlu tabel polymorphic.
 *
 * Desain kunci:
 * - `goal_type`:  "nominal" untuk rupiah, "unit" untuk barang/paket
 *                 sehingga progress bar bisa dihitung generik di frontend.
 * - `config`:     JSON opsional per campaign-type. Contoh untuk wakaf:
 *                 {"land_size": "200m2", "price_per_sqm": 500000}
 *                 Untuk zakat fitrah: {"rice_kg": 2.5, "alternative_cash": 40000}
 * - `collected_*` denormalized dari tabel donations untuk performa read;
 *                 diupdate via observer/event setiap donasi masuk.
 * - `end_date`    diindex karena sering dipakai untuk query "masih aktif".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
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
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('featured_image')->nullable();

            $table->string('type', 30)->default('donation')->index();
            $table->string('status', 30)->default('draft')->index();

            // ----- Goal Config -----
            // Tipe progress: nominal (Rp) atau unit (paket, kg, dll)
            $table->string('progress_type', 20)->default('amount');

            // Target campaign
            $table->unsignedBigInteger('target_amount')->nullable();
            $table->unsignedInteger('target_unit')->nullable();
            $table->string('unit_label', 30)->nullable();            // mis. "paket", "kg", "m²"

            // Progress (denormalized, diupdate lewat observer)
            $table->unsignedBigInteger('collected_amount')->default(0);
            $table->unsignedInteger('collected_unit')->default(0);
            $table->unsignedInteger('verified_donor_count')->default(0);

            // Periode campaign
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Konfigurasi dinamis per jenis campaign (opsional)
            // Contoh zakat fitrah: {"rice_or_cash": true, "cash_equivalent": 40000}
            // Contoh wakaf tanah: {"land_area_sqm": 200, "price_per_sqm": 500000}
            $table->json('config')->nullable();

            // Rekening penerima / payment gateway config
            $table->json('payment_config')->nullable();

            // Penerima / beneficiary
            $table->string('beneficiary_name')->nullable();
            $table->text('beneficiary_description')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('allow_anonymous')->default(true);  // boleh donasi anonim
            $table->boolean('show_donor_list')->default(true);  // tampilkan daftar donatur

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['status', 'end_date']);
            $table->index(['institution_id', 'status']);
            $table->index(['progress_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
