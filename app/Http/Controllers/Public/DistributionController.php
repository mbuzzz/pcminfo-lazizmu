<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Distribution;
use Illuminate\Contracts\View\View;

class DistributionController extends Controller
{
    public function index(): View
    {
        return view('public.distributions.index', [
            'distributions' => Distribution::query()
                ->with(['campaign', 'institution'])
                ->whereIn('status', ['distributed', 'reported'])
                ->latest('distribution_date')
                ->paginate(12),
            'pageTitle' => 'Laporan Penyaluran',
            'pageDescription' => 'Transparansi penyaluran program dan dampak yang telah direalisasikan.',
        ]);
    }
}
