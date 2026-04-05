<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('periode')->toString();

        return view('public.leaders.index', [
            'leaders' => Leader::query()
                ->with('institution')
                ->where('status', 'active')
                ->when($period !== '', fn ($query) => $query->where('period', $period))
                ->orderBy('period', 'desc')
                ->orderBy('order')
                ->paginate(18)
                ->withQueryString(),
            'periods' => Leader::query()
                ->whereNotNull('period')
                ->distinct()
                ->orderByDesc('period')
                ->pluck('period'),
            'selectedPeriod' => $period,
            'pageTitle' => 'Struktur Pimpinan',
            'pageDescription' => 'E-struktur pimpinan PCM Genteng dan unit terkait.',
        ]);
    }
}
