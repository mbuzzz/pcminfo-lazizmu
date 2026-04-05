<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Campaign\Actions\CreateCampaignAction;
use App\Domain\Campaign\Data\CampaignData;
use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Campaign\Enums\CampaignTypeEnum;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignProgressService;
use App\Domain\Donation\Actions\ApproveDonationAction;
use App\Domain\Donation\Actions\RejectDonationAction;
use App\Domain\Donation\Actions\SubmitDonationAction;
use App\Domain\Donation\Data\DonationData;
use App\Domain\Donation\Data\VerifyDonationData;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_based_campaign_is_updated_after_manual_approval(): void
    {
        $campaign = app(CreateCampaignAction::class)->execute(new CampaignData(
            type: CampaignTypeEnum::Donation,
            title: 'Donasi Ambulans',
            slug: 'donasi-ambulans',
            status: CampaignStatusEnum::Active,
            progressType: CampaignProgressTypeEnum::Amount,
            targetAmount: 100_000_000,
            config: [
                'version' => 1,
                'type' => 'donation',
                'form' => [
                    'fields' => [
                        [
                            'name' => 'payer_name',
                            'type' => 'text',
                            'label' => 'Nama Donatur',
                            'required' => true,
                            'rules' => ['string', 'max:100'],
                        ],
                        [
                            'name' => 'amount',
                            'type' => 'currency',
                            'label' => 'Nominal',
                            'required' => true,
                            'rules' => ['integer', 'min:10000'],
                        ],
                    ],
                ],
                'behavior' => [
                    'requires_manual_verification' => true,
                    'allow_custom_amount' => true,
                ],
                'progress' => [
                    'type' => 'amount',
                    'target_amount' => 100_000_000,
                ],
            ],
        ));

        $donation = app(SubmitDonationAction::class)->execute(new DonationData(
            campaignId: $campaign->getKey(),
            payerName: 'Ahmad',
            payerEmail: 'ahmad@example.test',
            donorPhone: '081234567890',
            payload: [
                'payer_name' => 'Ahmad',
                'amount' => 50_000,
            ],
            message: 'Semoga bermanfaat.',
        ));

        $admin = User::factory()->create();

        app(ApproveDonationAction::class)->execute(
            $donation,
            $admin,
            new VerifyDonationData(notes: 'Bukti transfer valid.'),
        );

        $campaign->refresh();
        $donation->refresh();

        $this->assertSame(DonationStatusEnum::Verified, $donation->status);
        $this->assertSame(50_000, $donation->amount);
        $this->assertSame(50_000, $campaign->collected_amount);
        $this->assertSame(0, $campaign->collected_unit);
        $this->assertSame(1, $campaign->verified_donor_count);
        $this->assertDatabaseCount('donation_verifications', 1);
        $this->assertDatabaseCount('campaign_progress_snapshots', 1);
    }

    public function test_qurban_campaign_resolves_unit_option_and_updates_unit_progress(): void
    {
        $campaign = app(CreateCampaignAction::class)->execute(new CampaignData(
            type: CampaignTypeEnum::Qurban,
            title: 'Qurban 1447 H',
            slug: 'qurban-1447-h',
            status: CampaignStatusEnum::Active,
            progressType: CampaignProgressTypeEnum::Unit,
            targetUnit: 7,
            unitLabel: 'bagian',
            config: [
                'version' => 1,
                'type' => 'qurban',
                'form' => [
                    'fields' => [
                        [
                            'name' => 'payer_name',
                            'type' => 'text',
                            'label' => 'Nama Donatur',
                            'required' => true,
                            'rules' => ['string', 'max:100'],
                        ],
                        [
                            'name' => 'selected_unit_option_code',
                            'type' => 'unit_option',
                            'label' => 'Paket Qurban',
                            'required' => true,
                            'rules' => ['string'],
                        ],
                        [
                            'name' => 'shohibul_qurban_name',
                            'type' => 'text',
                            'label' => 'Atas Nama',
                            'required' => true,
                            'rules' => ['string', 'max:100'],
                        ],
                    ],
                ],
                'behavior' => [
                    'requires_manual_verification' => true,
                    'allow_custom_amount' => false,
                ],
                'progress' => [
                    'type' => 'unit',
                    'target_unit' => 7,
                    'unit_label' => 'bagian',
                ],
            ],
            unitOptions: [
                [
                    'code' => 'sapi_1_7',
                    'label' => 'Sapi 1/7',
                    'unit_value' => 1,
                    'amount' => 3_000_000,
                ],
            ],
        ));

        $donation = app(SubmitDonationAction::class)->execute(new DonationData(
            campaignId: $campaign->getKey(),
            payerName: 'Siti',
            payerEmail: 'siti@example.test',
            donorPhone: '081111111111',
            payload: [
                'payer_name' => 'Siti',
                'selected_unit_option_code' => 'sapi_1_7',
                'shohibul_qurban_name' => 'Siti Aisyah',
            ],
        ));

        $admin = User::factory()->create();

        app(ApproveDonationAction::class)->execute(
            $donation,
            $admin,
            new VerifyDonationData(notes: 'Terverifikasi admin.'),
        );

        $campaign->refresh();
        $donation->refresh();

        $this->assertSame(3_000_000, $donation->amount);
        $this->assertSame(1, $donation->quantity);
        $this->assertSame('bagian', $donation->unit_label);
        $this->assertSame(1, $campaign->collected_unit);
        $this->assertSame(3_000_000, $campaign->collected_amount);
    }

    public function test_rejected_donation_does_not_change_campaign_progress(): void
    {
        $campaign = app(CreateCampaignAction::class)->execute(new CampaignData(
            type: CampaignTypeEnum::Donation,
            title: 'Renovasi Masjid',
            slug: 'renovasi-masjid',
            status: CampaignStatusEnum::Active,
            progressType: CampaignProgressTypeEnum::Amount,
            targetAmount: 75_000_000,
            config: [
                'version' => 1,
                'type' => 'donation',
                'form' => [
                    'fields' => [
                        [
                            'name' => 'payer_name',
                            'type' => 'text',
                            'label' => 'Nama Donatur',
                            'required' => true,
                            'rules' => ['string', 'max:100'],
                        ],
                        [
                            'name' => 'amount',
                            'type' => 'currency',
                            'label' => 'Nominal',
                            'required' => true,
                            'rules' => ['integer', 'min:10000'],
                        ],
                    ],
                ],
                'behavior' => [
                    'requires_manual_verification' => true,
                    'allow_custom_amount' => true,
                ],
                'progress' => [
                    'type' => 'amount',
                    'target_amount' => 75_000_000,
                ],
            ],
        ));

        $donation = app(SubmitDonationAction::class)->execute(new DonationData(
            campaignId: $campaign->getKey(),
            payerName: 'Budi',
            payerEmail: 'budi@example.test',
            donorPhone: '082222222222',
            payload: [
                'payer_name' => 'Budi',
                'amount' => 20_000,
            ],
        ));

        $admin = User::factory()->create();

        app(RejectDonationAction::class)->execute(
            $donation,
            $admin,
            new VerifyDonationData(reason: 'Bukti transfer tidak sesuai.', notes: 'Nominal tidak cocok.'),
        );

        $campaign->refresh();
        $donation->refresh();

        $this->assertSame(DonationStatusEnum::Rejected, $donation->status);
        $this->assertSame(0, $campaign->collected_amount);
        $this->assertSame(0, $campaign->verified_donor_count);
        $this->assertDatabaseHas('donation_verifications', [
            'donation_id' => $donation->getKey(),
            'status' => 'rejected',
        ]);
    }

    public function test_progress_can_be_recalculated_from_verified_donations(): void
    {
        $campaign = Campaign::query()->create([
            'title' => 'Beasiswa Santri',
            'slug' => 'beasiswa-santri',
            'type' => CampaignTypeEnum::Program,
            'status' => CampaignStatusEnum::Active,
            'progress_type' => CampaignProgressTypeEnum::Amount,
            'target_amount' => 10_000_000,
            'config' => [
                'version' => 1,
                'type' => 'program',
                'form' => [
                    'fields' => [
                        [
                            'name' => 'payer_name',
                            'type' => 'text',
                            'rules' => ['string'],
                        ],
                    ],
                ],
                'behavior' => [
                    'requires_manual_verification' => true,
                    'allow_custom_amount' => true,
                ],
                'progress' => [
                    'type' => 'amount',
                    'target_amount' => 10_000_000,
                ],
            ],
        ]);

        $campaign->donations()->createMany([
            [
                'transaction_code' => 'DON-TEST-0001',
                'payer_name' => 'Donatur 1',
                'amount' => 100_000,
                'quantity' => 0,
                'status' => DonationStatusEnum::Verified,
                'verified_at' => now(),
                'submitted_at' => now(),
                'payment_method' => 'manual_transfer',
            ],
            [
                'transaction_code' => 'DON-TEST-0002',
                'payer_name' => 'Donatur 2',
                'amount' => 200_000,
                'quantity' => 0,
                'status' => DonationStatusEnum::Verified,
                'verified_at' => now(),
                'submitted_at' => now(),
                'payment_method' => 'manual_transfer',
            ],
        ]);

        $campaign->update([
            'collected_amount' => 0,
            'verified_donor_count' => 0,
        ]);

        app(CampaignProgressService::class)->recalculate($campaign);

        $campaign->refresh();

        $this->assertSame(300_000, $campaign->collected_amount);
        $this->assertSame(2, $campaign->verified_donor_count);
    }
}
