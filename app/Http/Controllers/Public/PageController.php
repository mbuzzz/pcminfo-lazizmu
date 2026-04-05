<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function about(): View
    {
        $page = Page::query()->where('slug', 'tentang')->first();

        if ($page) {
            return $this->renderPage($page, request());
        }

        return view('public.pages.about', [
            'pageTitle' => 'Tentang Portal Digital',
            'pageDescription' => 'Profil singkat PCM Genteng, Lazismu, dan arah gerakan digital yang sedang dibangun.',
        ]);
    }

    public function contact(): View
    {
        $page = Page::query()->where('slug', 'kontak')->first();

        if ($page) {
            return $this->renderPage($page, request());
        }

        return view('public.pages.contact', [
            'pageTitle' => 'Kontak & Informasi',
            'pageDescription' => 'Informasi kontak resmi Portal Digital PCM Genteng & Lazismu.',
        ]);
    }

    public function show(Page $page, Request $request): View
    {
        return $this->renderPage($page, $request);
    }

    private function renderPage(Page $page, Request $request): View
    {
        $isPreview = $request->boolean('preview');

        if (! $page->isPublished()) {
            $user = Auth::user();

            abort_unless(
                $isPreview
                    && $user instanceof \App\Models\User
                    && $user->can('manage_pages'),
                404,
            );
        }

        return view('public.pages.show', [
            'page' => $page,
            'pageTitle' => $page->meta_title ?: $page->title,
            'pageDescription' => $page->meta_description ?: $page->excerpt,
        ]);
    }
}
