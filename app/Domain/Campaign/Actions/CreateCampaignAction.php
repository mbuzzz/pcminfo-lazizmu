<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Actions;

use App\Domain\Campaign\Data\CampaignData;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignService;
use Illuminate\Support\Facades\DB;

final class CreateCampaignAction
{
    public function __construct(
        private readonly CampaignService $campaignService,
    ) {}

    public function execute(CampaignData $data): Campaign
    {
        return DB::transaction(
            fn (): Campaign => $this->campaignService->create($data),
        );
    }
}
