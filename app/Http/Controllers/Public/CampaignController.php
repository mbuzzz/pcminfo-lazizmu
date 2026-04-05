<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Distribution;
use Illuminate\Contracts\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        return view('public.campaigns.index', [
            'pageTitle' => 'Program Donasi',
            'pageDescription' => 'Program filantropi, zakat, qurban, wakaf, dan donasi dari Lazismu.',
        ]);
    }

    public function category(Category $category): View
    {
        abort_unless($category->type === 'campaign' && $category->is_active, 404);

        return view('public.campaigns.category', [
            'category' => $category,
            'pageTitle' => 'Kategori Program: ' . $category->name,
            'pageDescription' => $category->description ?: 'Program filantropi pada kategori ' . $category->name . '.',
        ]);
    }

    public function show(Campaign $campaign): View
    {
        abort_unless(in_array($campaign->status->value, ['active', 'completed'], true), 404);

        return view('public.campaigns.show', [
            'campaign' => $campaign->loadMissing(['category', 'institution', 'updates', 'distributions']),
            'latestDistributions' => Distribution::query()
                ->where('campaign_id', $campaign->getKey())
                ->whereIn('status', ['distributed', 'reported'])
                ->latest('distribution_date')
                ->limit(3)
                ->get(),
            'pageTitle' => $campaign->seo_title,
            'pageDescription' => $campaign->seo_description,
            'pageImage' => $campaign->featured_image_url,
        ]);
    }
}
