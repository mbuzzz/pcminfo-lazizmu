<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * POSTS / BERITA & ARTIKEL
 *
 * Artikel berita, pengumuman, kajian, dan konten editorial lainnya.
 * `type` memisahkan jenis konten tanpa perlu tabel terpisah.
 * `author_id` meng-nullable agar konten bisa diatribusikan ke user lama
 * yang sudah dihapus tanpa merusak postingan.
 * `published_at` terpisah dari created_at agar bisa scheduled publishing.
 * `view_count` denormalized — tidak perlu join ke tabel views hanya untuk
 * menampilkan angka baca di list artikel.
 * Full-text: content disimpan sebagai longText untuk artikel panjang
 * dan mendukung block-editor (mis. TipTap/Quill JSON atau HTML).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Opsional: dikaitkan ke lembaga jika berita dari lembaga tertentu
            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('excerpt')->nullable();    // ringkasan pendek untuk card
            $table->longText('content');
            $table->string('featured_image')->nullable();

            $table->enum('type', [
                'news',          // Berita
                'article',       // Artikel / Opini
                'announcement',  // Pengumuman
                'study',         // Kajian / Materi keagamaan
            ])->default('news')->index();

            $table->enum('status', [
                'draft',
                'review',      // Menunggu persetujuan editor
                'published',
                'archived',
            ])->default('draft')->index();

            $table->timestamp('published_at')->nullable()->index();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Statistik denormalized
            $table->unsignedBigInteger('view_count')->default(0);

            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('allow_comments')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index(['author_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
