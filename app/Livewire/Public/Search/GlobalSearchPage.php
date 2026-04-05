<?php

declare(strict_types=1);

namespace App\Livewire\Public\Search;

use App\Enums\AgendaStatus;
use App\Enums\PostStatus;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalSearchPage extends Component
{
    use WithPagination;

    private const TYPES = ['all', 'posts', 'agendas', 'campaigns', 'institutions'];

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'tipe')]
    public string $type = 'all';

    public function mount(?string $initialSearch = null, ?string $initialType = null): void
    {
        if ($this->search === '' && filled($initialSearch)) {
            $this->search = trim((string) $initialSearch);
        }

        if ($this->type === 'all' && filled($initialType) && in_array($initialType, self::TYPES, true)) {
            $this->type = $initialType;
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

    public function clearFilters(): void
    {
        $this->reset(['search', 'type']);
        $this->resetPage();
    }

    public function render(): View
    {
        if (! in_array($this->type, self::TYPES, true)) {
            $this->type = 'all';
        }

        $postQuery = Post::query()
            ->with('category')
            ->published()
            ->when($this->hasSearch(), fn (Builder $query): Builder => $this->applySearch(
                $query,
                ['title', 'excerpt', 'content'],
            ))
            ->latest('published_at');

        $agendaQuery = Agenda::query()
            ->with('category')
            ->where('status', AgendaStatus::Published)
            ->when($this->hasSearch(), fn (Builder $query): Builder => $this->applySearch(
                $query,
                ['title', 'description', 'location_name', 'location_address'],
            ))
            ->orderBy('start_at');

        $campaignQuery = Campaign::query()
            ->with(['category', 'institution'])
            ->whereIn('status', ['active', 'completed'])
            ->when($this->hasSearch(), fn (Builder $query): Builder => $this->applySearch(
                $query,
                ['title', 'short_description', 'description'],
            ))
            ->orderByDesc('is_featured')
            ->latest();

        $institutionQuery = Institution::query()
            ->active()
            ->when($this->hasSearch(), fn (Builder $query): Builder => $this->applySearch(
                $query,
                ['name', 'tagline', 'description', 'address'],
            ))
            ->orderBy('name');

        /** @var array<string, int> $counts */
        $counts = [
            'all' => (clone $postQuery)->count()
                + (clone $agendaQuery)->count()
                + (clone $campaignQuery)->count()
                + (clone $institutionQuery)->count(),
            'posts' => (clone $postQuery)->count(),
            'agendas' => (clone $agendaQuery)->count(),
            'campaigns' => (clone $campaignQuery)->count(),
            'institutions' => (clone $institutionQuery)->count(),
        ];

        /** @var array<string, Collection<int, mixed>> $sections */
        $sections = [
            'posts' => collect(),
            'agendas' => collect(),
            'campaigns' => collect(),
            'institutions' => collect(),
        ];

        $results = null;

        if ($this->type === 'all') {
            $sections['posts'] = (clone $postQuery)->limit(4)->get();
            $sections['agendas'] = (clone $agendaQuery)->limit(4)->get();
            $sections['campaigns'] = (clone $campaignQuery)->limit(4)->get();
            $sections['institutions'] = (clone $institutionQuery)->limit(4)->get();
        } else {
            $results = match ($this->type) {
                'posts' => (clone $postQuery)->paginate(12, pageName: 'page'),
                'agendas' => (clone $agendaQuery)->paginate(12, pageName: 'page'),
                'campaigns' => (clone $campaignQuery)->paginate(12, pageName: 'page'),
                'institutions' => (clone $institutionQuery)->paginate(12, pageName: 'page'),
                default => null,
            };
        }

        return view('livewire.public.search.global-search-page', [
            'counts' => $counts,
            'sections' => $sections,
            'results' => $results,
            'tabs' => [
                'all' => 'Semua',
                'posts' => 'Berita',
                'agendas' => 'Agenda',
                'campaigns' => 'Program',
                'institutions' => 'Amal Usaha',
            ],
        ]);
    }

    /**
     * @param  list<string>  $columns
     */
    private function applySearch(Builder $query, array $columns): Builder
    {
        return $query->where(function (Builder $searchQuery) use ($columns): void {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $searchQuery->where($column, 'like', '%' . $this->search . '%');

                    continue;
                }

                $searchQuery->orWhere($column, 'like', '%' . $this->search . '%');
            }
        });
    }

    private function hasSearch(): bool
    {
        return trim($this->search) !== '';
    }
}
