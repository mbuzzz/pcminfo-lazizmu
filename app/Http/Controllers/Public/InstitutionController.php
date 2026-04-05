<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('jenis')->toString();

        return view('public.institutions.index', [
            'institutions' => Institution::query()
                ->with('activeLeaders')
                ->where('status', 'active')
                ->when($type !== '', fn ($query) => $query->where('type', $type))
                ->orderByDesc('is_featured')
                ->orderBy('order')
                ->paginate(12)
                ->withQueryString(),
            'selectedType' => $type,
            'pageTitle' => 'Direktori Amal Usaha',
            'pageDescription' => 'Direktori amal usaha, sekolah, layanan, dan unit sosial di lingkungan PCM Genteng.',
        ]);
    }

    public function show(Institution $institution): View
    {
        return view('public.institutions.show', [
            'institution' => $institution->loadMissing(['activeLeaders', 'posts', 'agendas']),
            'pageTitle' => $institution->name,
            'pageDescription' => $institution->tagline ?: str($institution->description)->limit(160)->toString(),
            'pageImage' => $institution->cover_image_url ?: $institution->logo_url,
        ]);
    }
}
