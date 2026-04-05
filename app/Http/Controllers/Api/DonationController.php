<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Donation\Actions\RejectDonationAction;
use App\Domain\Donation\Actions\VerifyDonationAction;
use App\Domain\Donation\Data\DonationData;
use App\Domain\Donation\Data\VerifyDonationData;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Services\DonationSubmissionService;
use App\Domain\Donation\Services\DonationWhatsAppLinkService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class DonationController extends Controller
{
    public function __construct(
        private readonly DonationSubmissionService $submissionService,
        private readonly DonationWhatsAppLinkService $whatsAppLinkService,
    ) {}

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $idempotencyKey = $request->header('X-Idempotency-Key') ?: $request->string('idempotency_key')->toString();

        if (! is_string($idempotencyKey) || trim($idempotencyKey) === '') {
            return response()->json([
                'message' => 'Idempotency key wajib diisi (header X-Idempotency-Key atau field idempotency_key).',
                'errors' => [
                    'idempotency_key' => ['Idempotency key wajib diisi.'],
                ],
            ], 422);
        }

        $existing = Donation::query()
            ->with('campaign')
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing) {
            return $this->donationResponse($existing);
        }

        $payload = (array) ($request->input('payload') ?? []);
        $payerPhone = $this->normalizeIndonesianPhone($request->string('payer_phone')->toString());

        try {
            $donation = $this->submissionService->submit(new DonationData(
                campaignId: (int) $request->integer('campaign_id'),
                payerName: $request->string('payer_name')->toString(),
                payerEmail: $request->input('payer_email'),
                donorPhone: $payerPhone,
                payload: $payload,
                userId: $request->user()?->getKey(),
                donorId: null,
                amount: $request->input('amount') !== null ? (int) $request->integer('amount') : null,
                quantity: $request->input('quantity') !== null ? (int) $request->integer('quantity') : null,
                paymentMethod: 'qris',
                paymentChannel: 'qris_static',
                message: $request->input('message'),
                isAnonymous: (bool) $request->boolean('is_anonymous'),
                meta: [],
                idempotencyKey: $idempotencyKey,
            ));
        } catch (QueryException $exception) {
            $duplicate = Donation::query()
                ->with('campaign')
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($duplicate) {
                return $this->donationResponse($duplicate);
            }

            throw $exception;
        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $donation->load('campaign');

        return $this->donationResponse($donation, 201);
    }

    public function show(string $transactionCode): JsonResponse
    {
        $donation = Donation::query()
            ->with('campaign')
            ->where('transaction_code', $transactionCode)
            ->firstOrFail();

        return $this->donationResponse($donation);
    }

    public function redirectToWhatsApp(string $transactionCode): RedirectResponse
    {
        $donation = Donation::query()
            ->with('campaign')
            ->where('transaction_code', $transactionCode)
            ->firstOrFail();

        $campaign = $donation->campaign;

        $directUrl = $this->whatsAppLinkService->buildConfirmUrl($donation, $campaign);
        $message = $this->whatsAppLinkService->buildMessage($donation, $campaign);

        abort_if(blank($directUrl), 404, 'Nomor WhatsApp konfirmasi belum dikonfigurasi.');

        $donationMeta = (array) ($donation->meta ?? []);
        $donationMeta['whatsapp'] = array_merge((array) ($donationMeta['whatsapp'] ?? []), [
            'confirm_message' => $message,
            'clicked_at' => now()->toISOString(),
        ]);

        $donation->forceFill([
            'meta' => $donationMeta,
        ])->save();

        return redirect()->away($directUrl);
    }

    public function verify(Donation $donation, Request $request, VerifyDonationAction $action): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
            'meta' => ['nullable', 'array'],
        ]);

        $actor = $request->user();

        $result = $action->execute($donation, $actor, new VerifyDonationData(
            reason: null,
            notes: Arr::get($validated, 'notes'),
            meta: (array) (Arr::get($validated, 'meta') ?? []),
        ));

        return response()->json([
            'data' => $result->toArray(),
        ]);
    }

    public function reject(Donation $donation, Request $request, RejectDonationAction $action): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'meta' => ['nullable', 'array'],
        ]);

        $actor = $request->user();

        $result = $action->execute($donation, $actor, new VerifyDonationData(
            reason: (string) $validated['reason'],
            notes: Arr::get($validated, 'notes'),
            meta: (array) (Arr::get($validated, 'meta') ?? []),
        ));

        return response()->json([
            'data' => $result->toArray(),
        ]);
    }

    private function donationResponse(Donation $donation, int $status = 200): JsonResponse
    {
        $campaign = $donation->campaign;

        $qrisImageUrl = $this->whatsAppLinkService->resolveQrisStaticImageUrl($campaign);
        $whatsappDirectUrl = $this->whatsAppLinkService->buildConfirmUrl($donation, $campaign);

        return response()->json([
            'data' => [
                'id' => $donation->getKey(),
                'transaction_code' => $donation->transaction_code,
                'status' => $donation->status->value,
                'amount' => $donation->amount,
                'quantity' => $donation->quantity,
                'unit_label' => $donation->unit_label,
                'payer_name' => $donation->payer_name,
                'payer_email' => $donation->payer_email,
                'payer_phone' => $donation->payer_phone,
                'is_anonymous' => $donation->is_anonymous,
                'message' => $donation->message,
                'submitted_at' => $donation->submitted_at?->toISOString(),
                'verified_at' => $donation->verified_at?->toISOString(),
                'campaign' => [
                    'id' => $campaign->getKey(),
                    'title' => $campaign->title,
                    'slug' => $campaign->slug,
                    'type' => $campaign->type->value,
                    'progress_type' => $campaign->progress_type->value,
                ],
                'qris' => [
                    'static_image_url' => $qrisImageUrl,
                ],
                'instructions' => [
                    'text' => $this->whatsAppLinkService->resolveDonationInstruction($campaign),
                ],
                'whatsapp' => [
                    'number' => $this->whatsAppLinkService->resolveDonationWhatsAppNumber($campaign),
                    'confirm_url' => url("/api/donations/{$donation->transaction_code}/whatsapp"),
                    'direct_url' => $whatsappDirectUrl,
                    'message' => $whatsappDirectUrl ? $this->whatsAppLinkService->buildMessage($donation, $campaign) : null,
                ],
            ],
        ], $status);
    }

    private function normalizeIndonesianPhone(string $phone): ?string
    {
        $phone = preg_replace('/\s+/', '', $phone) ?? $phone;
        $phone = preg_replace('/[^0-9+]/', '', $phone) ?? $phone;

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        return $phone;
    }
}
