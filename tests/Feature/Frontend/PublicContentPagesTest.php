<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use App\Enums\AgendaStatus;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_tentang_dan_kontak_dapat_dirender(): void
    {
        $this->get(route('pages.about'))
            ->assertOk()
            ->assertSee('Tentang Portal Digital');

        $this->get(route('pages.contact'))
            ->assertOk()
            ->assertSee('Kontak & Informasi');
    }

    public function test_halaman_kategori_agenda_dapat_dirender(): void
    {
        $category = Category::query()->create([
            'name' => 'Kajian',
            'slug' => 'kajian',
            'type' => 'agenda',
            'is_active' => true,
        ]);

        Agenda::query()->create([
            'category_id' => $category->getKey(),
            'title' => 'Kajian Ahad Pagi',
            'slug' => 'kajian-ahad-pagi',
            'description' => 'Kajian rutin pekanan.',
            'type' => 'kajian',
            'status' => AgendaStatus::Published,
            'start_at' => now()->addWeek(),
        ]);

        $this->get(route('agendas.categories.show', $category))
            ->assertOk()
            ->assertSee('Kategori Agenda: Kajian')
            ->assertSee('Kajian Ahad Pagi');
    }

    public function test_halaman_program_dapat_dirender_dengan_livewire_browser(): void
    {
        $category = Category::query()->create([
            'name' => 'Program Sosial',
            'slug' => 'program-sosial',
            'type' => 'campaign',
            'is_active' => true,
        ]);

        Campaign::query()->create([
            'category_id' => $category->getKey(),
            'title' => 'Program Ketahanan Pangan',
            'slug' => 'program-ketahanan-pangan',
            'short_description' => 'Bantuan pangan untuk keluarga rentan.',
            'description' => '<p>Detail program.</p>',
            'type' => 'social',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 5000000,
            'collected_amount' => 1000000,
            'verified_donor_count' => 4,
        ]);

        $this->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Program Ketahanan Pangan')
            ->assertSee('Reset');

        $this->get(route('campaigns.categories.show', $category))
            ->assertOk()
            ->assertSee('Kategori Program: Program Sosial')
            ->assertSee('Program Ketahanan Pangan');
    }
}
