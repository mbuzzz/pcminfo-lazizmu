<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('kategori')->toString();
        $search = $request->string('q')->toString();
        $categories = $this->categoryQuery()->get();

        $posts = Post::query()
            ->with(['category', 'institution', 'author'])
            ->where('status', PostStatus::Published)
            ->when($categorySlug !== '', function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.posts.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
            'search' => $search,
            'pageTitle' => 'Berita & Artikel',
            'pageDescription' => 'Informasi terbaru PCM Genteng dan Lazismu.',
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->incrementView();
        $post->loadCount(['comments', 'reactions']);

        $relatedPosts = Post::query()
            ->with(['category', 'institution'])
            ->where('status', PostStatus::Published)
            ->whereKeyNot($post->getKey())
            ->when($post->category_id !== null, fn ($query) => $query->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.posts.show', [
            'post' => $post->loadMissing(['category', 'institution', 'author']),
            'relatedPosts' => $relatedPosts,
            'pageTitle' => $post->seo_title,
            'pageDescription' => $post->seo_description,
            'pageImage' => $post->seo_image_url,
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->type === 'post' && $category->is_active, 404);

        $posts = Post::query()
            ->with(['category', 'institution', 'author'])
            ->where('status', PostStatus::Published)
            ->where('category_id', $category->getKey())
            ->latest('published_at')
            ->paginate(9);

        return view('public.posts.category', [
            'category' => $category->loadMissing('parent'),
            'categories' => $this->categoryQuery()->get(),
            'posts' => $posts,
            'pageTitle' => 'Kategori: ' . $category->name,
            'pageDescription' => $category->description ?: 'Kumpulan berita dan artikel dalam kategori ' . $category->name . '.',
        ]);
    }

    private function categoryQuery(): Builder
    {
        return Category::query()
            ->where('type', 'post')
            ->where('is_active', true)
            ->withCount([
                'posts' => fn (Builder $query): Builder => $query->where('status', PostStatus::Published),
            ])
            ->orderBy('order')
            ->orderBy('name');
    }
}
