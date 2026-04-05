<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostReactionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoint_reaksi_mengembalikan_ringkasan_reaksi(): void
    {
        $post = $this->createPublishedPost();

        $this->getJson('/api/posts/' . $post->slug . '/reactions')
            ->assertOk()
            ->assertJsonPath('data.total', 0)
            ->assertJsonPath('data.counts.like', 0);
    }

    public function test_user_login_bisa_toggle_reaksi_artikel(): void
    {
        $post = $this->createPublishedPost();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/posts/' . $post->slug . '/reactions', [
                'type' => 'love',
            ])
            ->assertOk()
            ->assertJsonPath('data.current_user_reaction', 'love')
            ->assertJsonPath('data.counts.love', 1);

        $this->actingAs($user)
            ->postJson('/api/posts/' . $post->slug . '/reactions', [
                'type' => 'love',
            ])
            ->assertOk()
            ->assertJsonPath('data.current_user_reaction', null)
            ->assertJsonPath('data.counts.love', 0);
    }

    private function createPublishedPost(): Post
    {
        $category = Category::query()->create([
            'name' => 'Berita',
            'slug' => 'berita',
            'type' => 'post',
            'is_active' => true,
        ]);

        return Post::query()->create([
            'category_id' => $category->getKey(),
            'author_id' => User::factory()->create()->getKey(),
            'title' => 'Artikel Uji Reaksi',
            'slug' => 'artikel-uji-reaksi',
            'excerpt' => 'Ringkasan artikel.',
            'content' => '<p>Konten artikel.</p>',
            'type' => 'news',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
