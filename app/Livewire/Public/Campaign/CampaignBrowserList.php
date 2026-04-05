<?php

declare(strict_types=1);

namespace App\Livewire\Public\Campaign;

use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CampaignBrowserList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'jenis')]
    public string $type = '';

    #[Url(as: 'kategori')]
    public string $category = '';

    public string $heading = 'Program Donasi';

    public string $description = 'Program filantropi, zakat, qurban, wakaf, dan donasi dari Lazismu.';

    public ?string $categoryTitle = null;

    public function mount(string $heading = 'Program Donasi', string $description = 'Program filantropi, zakat, qurban, wakaf, dan donasi dari Lazismu.', ?string $initialCategory = null, ?string $categoryTitle = null): void
    {
        $this->heading = $heading;
        $this->description = $description;
        $this->categoryTitle = $categoryTitle;

        if ($initialCategory !== null && $this->category === '') {
            $this->category = $initialCategory;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'type', 'category']);
        $this->resetPage();
    }

    public function render(): View
    {
        $categories = Category::query()
            ->where('type', 'campaign')
            ->where('is_active', true)
            ->withCount([
                'campaigns' => fn (Builder $query): Builder => $query->whereIn('status', ['active', 'completed']),
            ])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $campaigns = Campaign::query()
            ->with(['category', 'institution'])
            ->whereIn('status', ['active', 'completed'])
            ->when($this->type !== '', fn (Builder $query): Builder => $query->where('type', $this->type))
            ->when($this->category !== '', function (Builder $query): void {
                $query->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $this->category));
            })
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $searchQuery): void {
                    $searchQuery
                        ->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('short_description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12);

        return view('livewire.public.campaign.campaign-browser-list', [
            'categories' => $categories,
            'campaigns' => $campaigns,
        ]);
    }
}
