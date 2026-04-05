<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PageCmsPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_requires_publish_or_preview_permission(): void
    {
        $page = Page::factory()->create([
            'slug' => 'tentang',
            'status' => PageStatus::Draft,
            'published_at' => null,
        ]);

        $this->get(route('pages.show', $page))
            ->assertNotFound();

        $this->get(route('pages.show', ['page' => $page, 'preview' => 1]))
            ->assertNotFound();

        $user = User::factory()->create();
        Permission::query()->create([
            'name' => 'manage_pages',
            'guard_name' => 'web',
        ]);
        $user->givePermissionTo('manage_pages');

        $this->actingAs($user)
            ->get(route('pages.show', ['page' => $page, 'preview' => 1]))
            ->assertOk()
            ->assertSee($page->title);
    }

    public function test_published_page_can_be_viewed_publicly(): void
    {
        $page = Page::factory()->create([
            'slug' => 'kontak',
            'status' => PageStatus::Published,
            'published_at' => now()->subMinute(),
        ]);

        $this->get(route('pages.show', $page))
            ->assertOk()
            ->assertSee($page->title);
    }
}
