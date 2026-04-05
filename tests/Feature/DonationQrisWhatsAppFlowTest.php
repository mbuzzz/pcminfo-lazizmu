<?php

namespace Tests\Feature;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Donation\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DonationQrisWhatsAppFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_donation_is_idempotent(): void
    {
        config()->set('services.whatsapp.admin_phone', '6281234567890');
        config()->set('services.qris.static_image_url', 'https://example.test/qris.png');

        $campaign = Campaign::query()->create([
            'title' => 'Campaign Test',
            'slug' => 'campaign-test',
            'type' => 'donation',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 100_000,
            'target_unit' => null,
            'unit_label' => null,
            'collected_amount' => 0,
            'collected_unit' => 0,
            'verified_donor_count' => 0,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'published_at' => now(),
            'closed_at' => null,
            'config' => [],
            'payment_config' => [],
            'beneficiary_name' => null,
            'beneficiary_description' => null,
            'allow_anonymous' => true,
            'show_donor_list' => true,
        ]);

        $payload = [
            'campaign_id' => $campaign->getKey(),
            'payer_name' => 'Budi',
            'payer_phone' => '081234567890',
            'amount' => 25_000,
            'payload' => [],
            'is_anonymous' => false,
        ];

        $idempotencyKey = 'b3d8ed4e-7af0-4ec8-a79d-366f53b4c9f7';

        $first = $this
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/donations', $payload);

        $first->assertStatus(201);
        $transactionCode = (string) $first->json('data.transaction_code');

        $second = $this
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/donations', $payload);

        $second->assertStatus(200);
        $second->assertJsonPath('data.transaction_code', $transactionCode);

        $this->assertSame(1, Donation::query()->count());
    }

    public function test_whatsapp_endpoint_redirects_to_wa_me(): void
    {
        config()->set('services.whatsapp.admin_phone', '6281234567890');

        $campaign = Campaign::query()->create([
            'title' => 'Campaign Test',
            'slug' => 'campaign-test',
            'type' => 'donation',
            'status' => 'active',
            'progress_type' => 'amount',
            'target_amount' => 100_000,
            'target_unit' => null,
            'unit_label' => null,
            'collected_amount' => 0,
            'collected_unit' => 0,
            'verified_donor_count' => 0,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'published_at' => now(),
            'closed_at' => null,
            'config' => [],
            'payment_config' => [],
            'beneficiary_name' => null,
            'beneficiary_description' => null,
            'allow_anonymous' => true,
            'show_donor_list' => true,
        ]);

        $idempotencyKey = '40e68dcc-f7ee-4b15-a301-8027dfe56b83';

        $response = $this
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson('/api/donations', [
                'campaign_id' => $campaign->getKey(),
                'payer_name' => 'Siti',
                'payer_phone' => '081234567890',
                'amount' => 10_000,
                'payload' => [],
            ]);

        $transactionCode = (string) $response->json('data.transaction_code');

        $redirect = $this->get("/api/donations/{$transactionCode}/whatsapp");

        $redirect->assertStatus(302);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', (string) $redirect->headers->get('Location'));
    }
}
