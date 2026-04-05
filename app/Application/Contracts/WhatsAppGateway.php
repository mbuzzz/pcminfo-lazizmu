<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface WhatsAppGateway
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function send(string $phoneNumber, string $message, array $context = []): void;
}
