<?php

use App\Http\Controllers\Public\AgendaController;
use App\Http\Controllers\Public\CampaignController;
use App\Http\Controllers\Public\DistributionController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InstitutionController;
use App\Http\Controllers\Public\LeaderController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PostController;
use App\Http\Controllers\Public\ServiceWorkerController;
use App\Http\Controllers\Public\WebManifestController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/manifest.webmanifest', WebManifestController::class)->name('pwa.manifest');
Route::get('/sw.js', ServiceWorkerController::class)->name('pwa.service-worker');

Route::prefix('berita')->name('posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/kategori/{category:slug}', [PostController::class, 'category'])
        ->name('categories.show');
    Route::get('/{post:slug}', [PostController::class, 'show'])->name('show');
});

Route::prefix('agenda')->name('agendas.')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('index');
    Route::get('/kategori/{category:slug}', [AgendaController::class, 'category'])
        ->name('categories.show');
    Route::get('/{agenda:slug}', [AgendaController::class, 'show'])->name('show');
});

Route::prefix('amal-usaha')->name('institutions.')->group(function () {
    Route::get('/', [InstitutionController::class, 'index'])->name('index');
    Route::get('/{institution:slug}', [InstitutionController::class, 'show'])->name('show');
});

Route::get('/struktur-pimpinan', [LeaderController::class, 'index'])->name('leaders.index');
Route::view('/pencarian', 'public.search.index')->name('search.index');

Route::prefix('program')->name('campaigns.')->group(function () {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
    Route::get('/kategori/{category:slug}', [CampaignController::class, 'category'])
        ->name('categories.show');
    Route::get('/{campaign:slug}', [CampaignController::class, 'show'])->name('show');
});

Route::get('/penyaluran', [DistributionController::class, 'index'])->name('distributions.index');
Route::get('/tentang', [PageController::class, 'about'])->name('pages.about');
Route::get('/kontak', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/halaman/{page:slug}', [PageController::class, 'show'])->name('pages.show');
