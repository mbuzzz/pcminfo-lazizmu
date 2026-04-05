<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Donation\Models\Donation;
use App\Domain\Setting\Services\SiteSettingService;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Str;

final class DonationWhatsAppLinkService
{
    public function __construct(
        private readonly SiteSettingService $siteSettingService,
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    public function buildConfirmUrl(Donation $donation, Campaign $campaign): ?string
    {
        $adminPhone = $this->resolveAdminPhone($campaign);

        if (trim($adminPhone) === '') {
            return null;
        }

        $message = $this->buildMessage($donation, $campaign);

        $normalizedPhone = $this->normalizePhoneForWaMe($adminPhone);

        if ($normalizedPhone === '') {
            return null;
        }

        $encoded = rawurlencode($message);

        return "https://wa.me/{$normalizedPhone}?text={$encoded}";
    }

    public function buildMessage(Donation $donation, Campaign $campaign): string
    {
        $paymentConfig = (array) ($campaign->payment_config ?? []);
        $templates = (array) config('services.whatsapp.templates', []);
        $defaultTemplate = (string) $this->siteSettingService->getWithDefault('donation_whatsapp_message_template');

        $template = (string) (
            $paymentConfig['whatsapp_message_template']
            ?? $templates[$campaign->type->value]
            ?? config('services.whatsapp.default_template')
            ?? $defaultTemplate
        );

        if ($template === '') {
            $template = "Assalamu’alaikum.\nSaya {payer_name} konfirmasi donasi untuk \"{campaign_title}\".\nKode: {transaction_code}\nNominal: {amount}\nMohon diverifikasi. Jazakumullah khair.";
        }

        $payload = [
            '{payer_name}' => $donation->is_anonymous ? 'Hamba Allah' : (string) $donation->payer_name,
            '{payer_phone}' => (string) ($donation->payer_phone ?? '-'),
            '{payer_email}' => (string) ($donation->payer_email ?? '-'),
            '{campaign_title}' => (string) $campaign->title,
            '{campaign_type}' => (string) $campaign->type->value,
            '{transaction_code}' => (string) $donation->transaction_code,
            '{amount}' => $this->formatIdr((int) $donation->amount),
            '{quantity}' => (string) ($donation->quantity ?? 0),
            '{unit_label}' => (string) ($donation->unit_label ?? $campaign->unit_label ?? 'unit'),
            ':payer_name' => $donation->is_anonymous ? 'Hamba Allah' : (string) $donation->payer_name,
            ':payer_phone' => (string) ($donation->payer_phone ?? '-'),
            ':payer_email' => (string) ($donation->payer_email ?? '-'),
            ':campaign_title' => (string) $campaign->title,
            ':campaign_type' => (string) $campaign->type->value,
            ':transaction_code' => (string) $donation->transaction_code,
            ':amount' => $this->formatIdr((int) $donation->amount),
            ':quantity' => (string) ($donation->quantity ?? 0),
            ':unit_label' => (string) ($donation->unit_label ?? $campaign->unit_label ?? 'unit'),
        ];

        return Str::of($template)->replace(array_keys($payload), array_values($payload))->toString();
    }

    public function resolveQrisStaticImageUrl(Campaign $campaign): ?string
    {
        $paymentConfig = (array) ($campaign->payment_config ?? []);

        $url = $paymentConfig['qris_static_image_url']
            ?? $paymentConfig['qris_image_url']
            ?? $paymentConfig['qris_image']
            ?? $this->siteSettingService->getWithDefault('qris_image')
            ?? config('services.qris.static_image_url');

        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        return $this->resolvePathOrUrl($url);
    }

    public function resolveDonationInstruction(Campaign $campaign): ?string
    {
        $paymentConfig = (array) ($campaign->payment_config ?? []);

        $instruction = $paymentConfig['instruction_text']
            ?? $paymentConfig['donation_instruction_text']
            ?? $this->siteSettingService->getWithDefault('donation_instruction_text');

        if (! is_string($instruction)) {
            return null;
        }

        $instruction = trim($instruction);

        return $instruction !== '' ? $instruction : null;
    }

    public function resolveDonationWhatsAppNumber(Campaign $campaign): ?string
    {
        $phone = $this->resolveAdminPhone($campaign);

        return trim($phone) !== '' ? $phone : null;
    }

    private function resolveAdminPhone(Campaign $campaign): string
    {
        $paymentConfig = (array) ($campaign->payment_config ?? []);
        $byType = (array) config('services.whatsapp.admin_phone_by_campaign_type', []);
        $phone = $paymentConfig['whatsapp_number']
            ?? $paymentConfig['donation_whatsapp_number']
            ?? $byType[$campaign->type->value]
            ?? $this->siteSettingService->getWithDefault('donation_whatsapp_number')
            ?? config('services.whatsapp.admin_phone');

        return is_string($phone) ? $phone : '';
    }

    private function normalizePhoneForWaMe(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone) ?? $phone;
        $phone = preg_replace('/[^0-9+]/', '', $phone) ?? $phone;

        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }

    private function formatIdr(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    private function resolvePathOrUrl(string $value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return $this->mediaUploadService->url($value) ?? $value;
    }
}
