<?php

declare(strict_types=1);

namespace App\Domain\Donation\Rules;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignFormValidationService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

final class ValidDonationPayload implements ValidationRule
{
    public function __construct(
        private readonly CampaignFormValidationService $validationService,
        private readonly Campaign $campaign,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $this->validationService->validatePayload($this->campaign, (array) $value);
        } catch (Throwable $throwable) {
            $fail($throwable->getMessage());
        }
    }
}
