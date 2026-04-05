<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengunjung_bisa_melihat_daftar_komentar_artikel(): void
    {
        [$post] = $this->createPublishedPost();
        $comment = PostComment::query()->create([
            'post_id' => $post->getKey(),
            'user_id' => User::factory()->create()->getKey(),
            'body' => 'Komentar pertama untuk artikel.',
        ]);

        $this->getJson('/api/posts/' . $post->slug . '/comments')
            ->assertOk()
            ->assertJsonPath('data.0.id', $comment->getKey())
            ->assertJsonPath('data.0.body', 'Komentar pertama untuk artikel.');
    }

    public function test_user_login_bisa_menambahkan_memperbarui_dan_menghapus_komentar(): void
    {
        [$post] = $this->createPublishedPost();
        $user = User::factory()->create();

        $storeResponse = $this->actingAs($user)
            ->postJson('/api/posts/' . $post->slug . '/comments', [
                'body' => 'Komentar dari user login.',
            ]);

        $storeResponse
            ->assertCreated()
            ->assertJsonPath('data.body', 'Komentar dari user login.');

        $commentId = $storeResponse->json('data.id');

        $this->actingAs($user)
            ->patchJson('/api/comments/' . $commentId, [
                'body' => 'Komentar yang sudah diperbarui.',
            ])
            ->assertOk()
            ->assertJsonPath('data.body', 'Komentar yang sudah diperbarui.');

        $this->actingAs($user)
            ->deleteJson('/api/comments/' . $commentId)
            ->assertOk()
            ->assertJsonPath('message', 'Komentar berhasil dihapus.');

        $this->assertDatabaseMissing('post_comments', [
            'id' => $commentId,
        ]);
    }

    public function test_user_lain_tidak_bisa_menghapus_komentar_yang_bukan_miliknya(): void
    {
        [$post] = $this->createPublishedPost();
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $comment = PostComment::query()->create([
            'post_id' => $post->getKey(),
            'user_id' => $owner->getKey(),
            'body' => 'Komentar milik owner.',
        ]);

        $this->actingAs($intruder)
            ->deleteJson('/api/comments/' . $comment->getKey())
            ->assertForbidden();
    }

    /**
     * @return array{0: Post, 1: Category}
     */
    private function createPublishedPost(): array
    {
        $category = Category::query()->create([
            'name' => 'Berita',
            'slug' => 'berita',
            'type' => 'post',
            'is_active' => true,
        ]);

        $post = Post::query()->create([
            'category_id' => $category->getKey(),
            'author_id' => User::factory()->create()->getKey(),
            'title' => 'Artikel Uji Komentar',
            'slug' => 'artikel-uji-komentar',
            'excerpt' => 'Ringkasan artikel.',
            'content' => '<p>Konten artikel.</p>',
            'type' => 'news',
            'status' => 'published',
            'published_at' => now(),
            'allow_comments' => true,
        ]);

        return [$post, $category];
    }
}
