<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use App\Enums\AgendaStatus;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSearchExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_menampilkan_slider_artikel_dari_backend(): void
    {
        $category = Category::query()->create([
            'name' => 'Kabar Persyarikatan',
            'slug' => 'kabar-persyarikatan',
            'type' => 'post',
            'is_active' => true,
        ]);

        Post::query()->create([
            'category_id' => $category->getKey(),
            'title' => 'Silaturahmi PCM dan Amal Usaha',
            'slug' => 'silaturahmi-pcm-dan-amal-usaha',
            'excerpt' => 'Ringkasan kegiatan silaturahmi terbaru.',
            'content' => '<p>Konten berita lengkap.</p>',
            'type' => 'news',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Artikel Terbaru')
            ->assertSee('Silaturahmi PCM dan Amal Usaha');
    }

    public function test_halaman_pencarian_global_menampilkan_hasil_dari_beberapa_modul(): void
    {
        $postCategory = Category::query()->create([
            'name' => 'Berita',
            'slug' => 'berita',
            'type' => 'post',
            'is_active' => true,
        ]);

        $campaignCategory = Category::query()->create([
            'name' => 'Sosial',
            'slug' => 'sosial',
            'type' => 'campaign',
            'is_active' => true,
        ]);

        Post::query()->create([
            'category_id' => $postCategory->getKey(),
            'title' => 'Beasiswa Untuk Santri',
            'slug' => 'beasiswa-untuk-santri',
            'excerpt' => 'Berita program beasiswa.',
            'content' => '<p>Konten beasiswa.</p>',
            'type' => 'news',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Agenda::query()->create([
            'title' => 'Kajian Santri Akhir Pekan',
            'slug' => 'kajian-santri-akhir-pekan',
            'description' => 'Agenda pembinaan santri.',
            'type' => 'kajian',
            'status' => AgendaStatus::Published,
            'start_at' => now()->addDays(2),
        ]);

        Campaign::query()->create([
            'category_id' => $campaignCategory->getKey(),
            'title' => 'Program Beasiswa Santri',
            'slug' => 'program-beasiswa-santri',
            'short_description' => 'Penguatan biaya pendidikan santri.',
            'description' => '<p>Program beasiswa santri.</p>',
            'type' => 'scholarship',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 10000000,
            'collected_amount' => 2500000,
            'verified_donor_count' => 12,
        ]);

        Institution::query()->create([
            'name' => 'Pesantren Santri Mandiri',
            'slug' => 'pesantren-santri-mandiri',
            'type' => 'school',
            'status' => 'active',
            'description' => 'Amal usaha berbasis pendidikan santri.',
        ]);

        $this->get(route('search.index', ['q' => 'santri']))
            ->assertOk()
            ->assertSee('Pencarian Global')
            ->assertSee('Beasiswa Untuk Santri')
            ->assertSee('Kajian Santri Akhir Pekan')
            ->assertSee('Program Beasiswa Santri')
            ->assertSee('Pesantren Santri Mandiri');
    }

    public function test_halaman_404_kustom_dirender_dengan_copy_yang_rapi(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman ini belum ditemukan')
            ->assertSee('Kembali ke Beranda');
    }
}
