<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class PostService
{
    public function create(array $data): Post
    {
        $data = $this->normalize($data);
        $data['author_id'] ??= auth()->id();
        $data['status'] ??= PostStatus::Draft;

        return Post::query()->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $featuredImageWasSubmitted = array_key_exists('featured_image', $data);
        $data = $this->normalize($data, $post);

        app(MediaUploadService::class)->sync(
            $post->featured_image,
            $data['featured_image'] ?? $post->featured_image,
            $featuredImageWasSubmitted,
        );

        $post->update($data);

        return $post->refresh();
    }

    public function submitReview(Post $post): Post
    {
        $post->update([
            'status' => PostStatus::Review,
        ]);

        return $post->refresh();
    }

    public function publish(Post $post, ?User $actor = null): Post
    {
        $post->update([
            'status' => PostStatus::Published,
            'published_at' => $post->published_at ?? now(),
            'author_id' => $post->author_id ?? $actor?->getKey() ?? auth()->id(),
        ]);

        return $post->refresh();
    }

    public function archive(Post $post): Post
    {
        $post->update([
            'status' => PostStatus::Archived,
        ]);

        return $post->refresh();
    }

    private function normalize(array $data, ?Post $post = null): array
    {
        $slug = filled(Arr::get($data, 'slug'))
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) Arr::get($data, 'title', $post?->title));

        $data['slug'] = $this->makeUniqueSlug($slug, $post);
        $data['meta_title'] = filled(Arr::get($data, 'meta_title'))
            ? trim((string) $data['meta_title'])
            : Arr::get($data, 'title', $post?->title);
        $data['meta_description'] = filled(Arr::get($data, 'meta_description'))
            ? trim((string) $data['meta_description'])
            : Arr::get($data, 'excerpt', $post?->excerpt);

        return $data;
    }

    private function makeUniqueSlug(string $slug, ?Post $post = null): string
    {
        $baseSlug = $slug !== '' ? $slug : 'post';
        $candidate = $baseSlug;
        $iteration = 2;

        while (
            Post::query()
                ->when($post !== null, fn ($query) => $query->whereKeyNot($post->getKey()))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$baseSlug}-{$iteration}";
            $iteration++;
        }

        return $candidate;
    }
}
