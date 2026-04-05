<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignProgressService;
use App\Domain\Donation\Services\DonationWhatsAppLinkService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignProgressService $progressService,
        private readonly DonationWhatsAppLinkService $donationWhatsAppLinkService,
    ) {}

    public function index(): JsonResponse
    {
        $campaigns = Campaign::query()
            ->publiclyAvailable()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $campaigns->map(function (Campaign $campaign): array {
                return [
                    'id' => $campaign->getKey(),
                    'title' => $campaign->title,
                    'slug' => $campaign->slug,
                    'type' => $campaign->type->value,
                    'status' => $campaign->status->value,
                    'progress_type' => $campaign->progress_type->value,
                    'target_amount' => $campaign->target_amount,
                    'target_unit' => $campaign->target_unit,
                    'unit_label' => $campaign->unit_label,
                    'collected_amount' => $campaign->collected_amount,
                    'collected_unit' => $campaign->collected_unit,
                    'verified_donor_count' => $campaign->verified_donor_count,
                    'progress' => $this->progressService->summarize($campaign),
                ];
            })->values(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $campaign = Campaign::query()
            ->publiclyAvailable()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $campaign->getKey(),
                'title' => $campaign->title,
                'slug' => $campaign->slug,
                'short_description' => $campaign->short_description,
                'description' => $campaign->description,
                'type' => $campaign->type->value,
                'status' => $campaign->status->value,
                'progress_type' => $campaign->progress_type->value,
                'target_amount' => $campaign->target_amount,
                'target_unit' => $campaign->target_unit,
                'unit_label' => $campaign->unit_label,
                'collected_amount' => $campaign->collected_amount,
                'collected_unit' => $campaign->collected_unit,
                'verified_donor_count' => $campaign->verified_donor_count,
                'start_date' => $campaign->start_date?->toDateString(),
                'end_date' => $campaign->end_date?->toDateString(),
                'payment_config' => $campaign->payment_config,
                'payment' => [
                    'qris_static_image_url' => $this->donationWhatsAppLinkService->resolveQrisStaticImageUrl($campaign),
                    'whatsapp_number' => $this->donationWhatsAppLinkService->resolveDonationWhatsAppNumber($campaign),
                    'instruction_text' => $this->donationWhatsAppLinkService->resolveDonationInstruction($campaign),
                ],
                'config' => $campaign->config,
                'progress' => $this->progressService->summarize($campaign),
            ],
        ]);
    }
}
