<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Enums\AgendaStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Distribution;
use App\Models\Institution;
use App\Models\Leader;
use App\Models\Post;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'featuredPosts' => Post::query()
                ->with(['category', 'institution'])
                ->where('status', PostStatus::Published)
                ->latest('published_at')
                ->limit(4)
                ->get(),
            'upcomingAgendas' => Agenda::query()
                ->with(['institution', 'category'])
                ->where('status', AgendaStatus::Published)
                ->where('start_at', '>=', now())
                ->orderBy('start_at')
                ->limit(4)
                ->get(),
            'featuredInstitutions' => Institution::query()
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('order')
                ->limit(6)
                ->get(),
            'leaders' => Leader::query()
                ->with('institution')
                ->where('status', 'active')
                ->orderBy('order')
                ->limit(6)
                ->get(),
            'featuredCampaigns' => Campaign::query()
                ->with(['category', 'institution'])
                ->whereIn('status', ['active', 'completed'])
                ->orderByDesc('is_featured')
                ->latest()
                ->limit(4)
                ->get(),
            'latestDistributions' => Distribution::query()
                ->with(['campaign', 'institution'])
                ->whereIn('status', ['distributed', 'reported'])
                ->latest('distribution_date')
                ->limit(3)
                ->get(),
            'stats' => [
                'posts' => Post::query()->where('status', PostStatus::Published)->count(),
                'agendas' => Agenda::query()->where('status', AgendaStatus::Published)->count(),
                'institutions' => Institution::query()->where('status', 'active')->count(),
                'campaigns' => Campaign::query()->where('status', 'active')->count(),
            ],
        ]);
    }
}
