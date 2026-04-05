<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CATEGORIES
 *
 * Dibuat sebagai tabel pertama karena digunakan oleh banyak modul lain
 * (posts, campaigns, agendas). Self-referencing parent_id memungkinkan
 * hierarki kategori bersarang. Field `type` memisahkan kategori antar modul
 * sehingga satu tabel bisa melayani semua, tanpa perlu tabel terpisah.
 * Slug diindex untuk query URL-friendly yang cepat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Self-referencing untuk hierarki kategori (mis. Berita > Nasional)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();        // icon class atau path gambar
            $table->string('color', 7)->nullable();    // hex color untuk UI badge

            // Memisahkan kategori antar modul tanpa perlu tabel sendiri-sendiri
            $table->enum('type', [
                'post',
                'campaign',
                'agenda',
                'distribution',
                'general',
            ])->default('general')->index();

            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            // Index komposit untuk query kategori aktif per type
            $table->index(['type', 'is_active']);
            $table->index(['parent_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
