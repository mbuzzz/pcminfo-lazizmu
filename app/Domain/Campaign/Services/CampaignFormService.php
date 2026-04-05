<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use InvalidArgumentException;

final class CampaignFormService
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function validate(array $config): void
    {
        $fields = data_get($config, 'form.fields', []);

        if (! is_array($fields) || $fields === []) {
            throw new InvalidArgumentException('Campaign config must contain at least one form field.');
        }

        foreach ($fields as $index => $field) {
            if (! isset($field['name'], $field['type'])) {
                throw new InvalidArgumentException("Campaign form field at index {$index} must contain name and type.");
            }
        }
    }
}
