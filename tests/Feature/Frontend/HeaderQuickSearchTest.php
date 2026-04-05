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
use Livewire\Livewire;
use Tests\TestCase;

class HeaderQuickSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_quick_search_menampilkan_hasil_lintas_modul(): void
    {
        $postCategory = Category::query()->create([
            'name' => 'Berita',
            'slug' => 'berita',
            'type' => 'post',
            'is_active' => true,
        ]);

        $campaignCategory = Category::query()->create([
            'name' => 'Program Sosial',
            'slug' => 'program-sosial',
            'type' => 'campaign',
            'is_active' => true,
        ]);

        Post::query()->create([
            'category_id' => $postCategory->getKey(),
            'title' => 'Santri Muda Menguatkan Dakwah',
            'slug' => 'santri-muda-menguatkan-dakwah',
            'excerpt' => 'Program pembinaan santri.',
            'content' => '<p>Konten santri.</p>',
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
            'start_at' => now()->addDays(3),
        ]);

        Campaign::query()->create([
            'category_id' => $campaignCategory->getKey(),
            'title' => 'Program Beasiswa Santri',
            'slug' => 'program-beasiswa-santri',
            'short_description' => 'Dukungan pendidikan santri.',
            'description' => '<p>Program beasiswa untuk santri.</p>',
            'type' => 'scholarship',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 10000000,
            'collected_amount' => 2500000,
            'verified_donor_count' => 15,
        ]);

        Institution::query()->create([
            'name' => 'Pesantren Santri Mandiri',
            'slug' => 'pesantren-santri-mandiri',
            'type' => 'school',
            'status' => 'active',
            'description' => 'Amal usaha untuk santri.',
        ]);

        Livewire::test('public.search.header-quick-search')
            ->set('query', 'santri')
            ->assertSee('Hasil cepat pencarian')
            ->assertSee('Santri Muda Menguatkan Dakwah')
            ->assertSee('Kajian Santri Akhir Pekan')
            ->assertSee('Program Beasiswa Santri')
            ->assertSee('Pesantren Santri Mandiri');
    }

    public function test_header_quick_search_tidak_menampilkan_hasil_saat_query_terlalu_pendek(): void
    {
        Livewire::test('public.search.header-quick-search')
            ->set('query', 's')
            ->assertDontSee('Santri Muda Menguatkan Dakwah')
            ->assertDontSee('Program Beasiswa Santri');
    }
}
