<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Campaign\Models\Campaign;

final class DonationMetaFactory
{
    /**
     * @param  array<string, mixed>  $validatedPayload
     * @param  array<string, mixed>  $resolvedAttributes
     * @return array<string, mixed>
     */
    public function fromPayload(Campaign $campaign, array $validatedPayload, array $resolvedAttributes = []): array
    {
        return [
            'campaign_type' => $campaign->type->value,
            'progress_type' => $campaign->progress_type->value,
            'form_payload' => $validatedPayload,
            'resolved' => $resolvedAttributes,
        ];
    }
}
