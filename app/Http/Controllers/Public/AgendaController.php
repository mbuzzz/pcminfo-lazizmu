<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\AgendaStatus;
use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('kategori')->toString();

        return view('public.agendas.index', [
            'agendas' => Agenda::query()
                ->with(['institution', 'category'])
                ->where('status', AgendaStatus::Published)
                ->when($categorySlug !== '', function (Builder $query) use ($categorySlug): void {
                    $query->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $categorySlug));
                })
                ->orderBy('start_at')
                ->paginate(9)
                ->withQueryString(),
            'categories' => $this->categoryQuery()->get(),
            'currentCategory' => $categorySlug,
            'pageTitle' => 'Agenda & Kegiatan',
            'pageDescription' => 'Agenda terbaru PCM Genteng dan ekosistem organisasinya.',
        ]);
    }

    public function show(Agenda $agenda): View
    {
        abort_unless($agenda->status === AgendaStatus::Published, 404);

        return view('public.agendas.show', [
            'agenda' => $agenda->loadMissing(['institution', 'category']),
            'pageTitle' => $agenda->seo_title,
            'pageDescription' => $agenda->seo_description,
            'pageImage' => $agenda->seo_image_url,
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->type === 'agenda' && $category->is_active, 404);

        return view('public.agendas.category', [
            'category' => $category->loadMissing('parent'),
            'categories' => $this->categoryQuery()->get(),
            'agendas' => Agenda::query()
                ->with(['institution', 'category'])
                ->where('status', AgendaStatus::Published)
                ->where('category_id', $category->getKey())
                ->orderBy('start_at')
                ->paginate(9),
            'pageTitle' => 'Kategori Agenda: ' . $category->name,
            'pageDescription' => $category->description ?: 'Agenda dan kegiatan pada kategori ' . $category->name . '.',
        ]);
    }

    private function categoryQuery(): Builder
    {
        return Category::query()
            ->where('type', 'agenda')
            ->where('is_active', true)
            ->withCount([
                'agendas' => fn (Builder $query): Builder => $query->where('status', AgendaStatus::Published),
            ])
            ->orderBy('order')
            ->orderBy('name');
    }
}
