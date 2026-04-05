<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Models\Campaign;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

final class CampaignFormValidationService
{
    public function __construct(
        private readonly CampaignConfigService $configService,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function validatePayload(Campaign $campaign, array $payload): array
    {
        $validator = Validator::make($payload, $this->rules($campaign), $this->messages($campaign));

        return $validator->validate();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(Campaign $campaign): array
    {
        $rules = [];

        foreach ($this->configService->getFormSchema($campaign) as $field) {
            $name = $field['name'] ?? null;

            if (! is_string($name) || $name === '') {
                throw new InvalidArgumentException('Campaign form field name must be a non-empty string.');
            }

            $fieldRules = is_array($field['rules'] ?? null) ? $field['rules'] : [];
            $fieldRules = array_map(static fn (mixed $rule): string => (string) $rule, $fieldRules);

            if (($field['required'] ?? false) === true && ! in_array('required', $fieldRules, true)) {
                array_unshift($fieldRules, 'required');
            }

            if (($field['required'] ?? false) !== true && ! in_array('nullable', $fieldRules, true)) {
                array_unshift($fieldRules, 'nullable');
            }

            $rules[$name] = array_values(array_unique($fieldRules));
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(Campaign $campaign): array
    {
        $messages = [];

        foreach ($this->configService->getFormSchema($campaign) as $field) {
            $name = $field['name'] ?? null;
            $label = $field['label'] ?? $name;

            if (! is_string($name) || $name === '') {
                continue;
            }

            $messages["{$name}.required"] = "{$label} wajib diisi.";
        }

        return $messages;
    }
}
