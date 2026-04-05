<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp;

use App\Application\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Log;

final class LogWhatsAppGateway implements WhatsAppGateway
{
    public function send(string $phoneNumber, string $message, array $context = []): void
    {
        Log::info('WhatsApp message queued.', [
            'phone_number' => $phoneNumber,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
