<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignConfigService;
use App\Domain\Campaign\Services\CampaignEligibilityService;
use App\Domain\Campaign\Services\CampaignFormValidationService;
use App\Domain\Donation\Data\DonationData;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Models\Donor;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class DonationSubmissionService
{
    public function __construct(
        private readonly CampaignEligibilityService $campaignEligibilityService,
        private readonly CampaignFormValidationService $formValidationService,
        private readonly CampaignConfigService $configService,
        private readonly CampaignUnitOptionResolver $unitOptionResolver,
        private readonly DonationMetaFactory $metaFactory,
    ) {}

    public function submit(DonationData $data): Donation
    {
        $campaign = Campaign::query()->with('unitOptions')->findOrFail($data->campaignId);
        $this->campaignEligibilityService->assertAcceptingDonation($campaign);

        $validatedPayload = $this->formValidationService->validatePayload($campaign, $data->payload);
        $behavior = $this->configService->getBehavior($campaign);

        $resolved = $this->resolveAmounts($campaign, $validatedPayload, $data);

        $donor = $this->resolveDonor($data);

        return Donation::query()->create([
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor?->getKey(),
            'user_id' => $data->userId,
            'transaction_code' => $this->generateTransactionCode($campaign),
            'idempotency_key' => $data->idempotencyKey,
            'payer_name' => $data->payerName,
            'payer_email' => $data->payerEmail,
            'payer_phone' => $data->donorPhone,
            'is_anonymous' => $data->isAnonymous,
            'amount' => $resolved['amount'],
            'quantity' => $resolved['quantity'],
            'unit_label' => $resolved['unit_label'],
            'message' => $data->message,
            'payment_method' => $data->paymentMethod,
            'payment_channel' => $data->paymentChannel,
            'status' => DonationStatusEnum::Pending,
            'submitted_at' => now(),
            'meta' => array_merge(
                $this->metaFactory->fromPayload($campaign, $validatedPayload, $resolved),
                $data->meta,
                [
                    'requires_manual_verification' => (bool) ($behavior['requires_manual_verification'] ?? true),
                ],
            ),
        ]);
    }

    private function resolveDonor(DonationData $data): ?Donor
    {
        if ($data->donorId !== null) {
            return Donor::query()->find($data->donorId);
        }

        if ($data->donorPhone === null && $data->payerEmail === null) {
            return null;
        }

        return Donor::query()->firstOrCreate(
            [
                'phone' => $data->donorPhone,
                'email' => $data->payerEmail,
            ],
            [
                'name' => $data->payerName,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $validatedPayload
     * @return array<string, int|string|null>
     */
    private function resolveAmounts(Campaign $campaign, array $validatedPayload, DonationData $data): array
    {
        $amount = $data->amount ?? 0;
        $quantity = $data->quantity ?? 0;
        $unitLabel = $campaign->unit_label;

        if ($campaign->progress_type === CampaignProgressTypeEnum::Unit) {
            $selectedOptionCode = $validatedPayload['selected_unit_option_code'] ?? null;

            if (! is_string($selectedOptionCode) || $selectedOptionCode === '') {
                throw new InvalidArgumentException('Unit-based campaign requires selected_unit_option_code.');
            }

            $option = $this->unitOptionResolver->resolve($campaign, $selectedOptionCode);
            $amount = (int) $option->amount;
            $quantity = (int) $option->unit_value;
            $unitLabel = $campaign->unit_label ?? 'unit';
        }

        if ($campaign->progress_type === CampaignProgressTypeEnum::Amount) {
            $allowCustomAmount = (bool) data_get($campaign->config, 'behavior.allow_custom_amount', true);

            if ($allowCustomAmount === false && $data->amount === null) {
                $amount = (int) ($validatedPayload['amount'] ?? 0);
            }

            $amount = $data->amount ?? (int) ($validatedPayload['amount'] ?? $amount);
            $quantity = max(0, (int) ($data->quantity ?? $quantity));
        }

        if ($campaign->progress_type === CampaignProgressTypeEnum::Amount && $amount <= 0) {
            throw new InvalidArgumentException('Amount-based campaign requires positive amount.');
        }

        if ($campaign->progress_type === CampaignProgressTypeEnum::Unit && $quantity <= 0) {
            throw new InvalidArgumentException('Unit-based campaign requires positive quantity.');
        }

        return [
            'amount' => $amount,
            'quantity' => $quantity,
            'unit_label' => $unitLabel,
        ];
    }

    private function generateTransactionCode(Campaign $campaign): string
    {
        return strtoupper(sprintf(
            '%s-%s',
            Str::limit(Str::upper(Str::slug($campaign->type->value, '')), 4, ''),
            Str::upper(Str::random(10)),
        ));
    }
}
