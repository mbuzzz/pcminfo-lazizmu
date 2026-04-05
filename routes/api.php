<?php

use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\PostCommentController;
use App\Http\Controllers\Api\PostReactionController;
use Illuminate\Support\Facades\Route;

Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/{slug}', [CampaignController::class, 'show']);

Route::get('/posts/{post:slug}/comments', [PostCommentController::class, 'index']);
Route::get('/posts/{post:slug}/reactions', [PostReactionController::class, 'show']);

Route::post('/donations', [DonationController::class, 'store']);
Route::get('/donations/{transactionCode}', [DonationController::class, 'show']);
Route::get('/donations/{transactionCode}/whatsapp', [DonationController::class, 'redirectToWhatsApp']);

Route::middleware(['web', 'auth', 'throttle:20,1'])->group(function (): void {
    Route::post('/posts/{post:slug}/comments', [PostCommentController::class, 'store']);
    Route::get('/comments/{postComment}', [PostCommentController::class, 'show']);
    Route::patch('/comments/{postComment}', [PostCommentController::class, 'update']);
    Route::delete('/comments/{postComment}', [PostCommentController::class, 'destroy']);

    Route::post('/posts/{post:slug}/reactions', [PostReactionController::class, 'store']);
});

Route::middleware(['web', 'auth', 'permission:verify_donations'])
    ->prefix('admin')
    ->group(function (): void {
        Route::post('/donations/{donation}/verify', [DonationController::class, 'verify']);
        Route::post('/donations/{donation}/reject', [DonationController::class, 'reject']);
    });
