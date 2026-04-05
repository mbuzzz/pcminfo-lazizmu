<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments;

use App\Application\Contracts\PaymentGateway;

final class NullPaymentGateway implements PaymentGateway
{
    public function createCharge(array $payload): array
    {
        return [
            'provider' => 'null',
            'status' => 'pending',
            'payload' => $payload,
        ];
    }
}
