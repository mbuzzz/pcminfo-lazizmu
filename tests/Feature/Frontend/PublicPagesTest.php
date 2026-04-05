<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Services\SettingService;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_dapat_dirender(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Portal Digital', false);
        $response->assertSee('aria-label="Donasi"', false);
    }

    public function test_halaman_berita_list_dan_detail_dirender_dengan_data(): void
    {
        $category = Category::query()->create([
            'name' => 'Berita PCM',
            'slug' => 'berita-pcm',
            'type' => 'post',
            'is_active' => true,
        ]);

        $post = Post::query()->create([
            'category_id' => $category->getKey(),
            'title' => 'Musyawarah PCM Genteng',
            'slug' => 'musyawarah-pcm-genteng',
            'excerpt' => 'Musyawarah tahunan berjalan lancar.',
            'content' => '<p>Konten detail berita.</p>',
            'type' => 'news',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSee('Musyawarah PCM Genteng');

        $this->get(route('posts.categories.show', $category))
            ->assertOk()
            ->assertSee('Kategori: Berita PCM')
            ->assertSee('Musyawarah PCM Genteng');

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('Konten detail berita.', false);
    }

    public function test_halaman_campaign_list_dan_detail_dirender_dengan_data(): void
    {
        app(SettingService::class)->put(SettingGroupEnum::App, 'donation_whatsapp_number', '6281234567890', isPublic: true);

        $category = Category::query()->create([
            'name' => 'Donasi Sosial',
            'slug' => 'donasi-sosial',
            'type' => 'campaign',
            'is_active' => true,
        ]);

        $institution = Institution::query()->create([
            'name' => 'Lazismu Genteng',
            'slug' => 'lazismu-genteng',
            'type' => 'finance',
            'status' => 'active',
        ]);

        $campaign = Campaign::query()->create([
            'category_id' => $category->getKey(),
            'institution_id' => $institution->getKey(),
            'title' => 'Program Beasiswa Santri',
            'slug' => 'program-beasiswa-santri',
            'short_description' => 'Membantu biaya pendidikan santri.',
            'description' => '<p>Detail program beasiswa.</p>',
            'type' => 'scholarship',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 10000000,
            'collected_amount' => 2500000,
            'verified_donor_count' => 12,
        ]);

        $this->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Program Beasiswa Santri');

        $this->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('Detail program beasiswa.', false)
            ->assertSee('Konfirmasi via WhatsApp');
    }

    public function test_halaman_agenda_list_dirender_dengan_data(): void
    {
        $agenda = Agenda::query()->create([
            'title' => 'Kajian Ahad Pagi',
            'slug' => 'kajian-ahad-pagi',
            'description' => 'Kajian rutin untuk jamaah.',
            'type' => 'kajian',
            'status' => 'published',
            'start_at' => now()->addWeek(),
        ]);

        $this->get(route('agendas.index'))
            ->assertOk()
            ->assertSee('Kajian Ahad Pagi');

        $this->get(route('agendas.show', $agenda))
            ->assertOk()
            ->assertSee('Kajian rutin untuk jamaah.');
    }
}
