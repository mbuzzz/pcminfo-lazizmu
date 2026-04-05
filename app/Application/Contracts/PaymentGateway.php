<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createCharge(array $payload): array;
}
