<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription" :page-image="$pageImage">
    @php
        $featuredImage = $campaign->featured_image_url;
        $donationFallbacks = app(\App\Domain\Setting\Services\SiteSettingService::class)->donationFallbacks();
        $qrisImage = data_get($campaign->payment_config, 'qris_image_url') ?: ($donationFallbacks['qris_image'] ? app(\App\Services\Media\MediaUploadService::class)->url($donationFallbacks['qris_image']) : null);
        $waNumber = data_get($campaign->payment_config, 'whatsapp_number') ?: $donationFallbacks['donation_whatsapp_number'];
        $instructionText = data_get($campaign->payment_config, 'instruction_text') ?: $donationFallbacks['donation_instruction_text'];
    @endphp

    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div>
            <a href="{{ route('campaigns.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke program</span>
            </a>
        </div>
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-red-600">{{ $campaign->type->getLabel() }}</div>
                        <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">{{ $campaign->status->getLabel() }}</div>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $campaign->title }}</h1>
                    @if ($campaign->short_description)
                        <p class="text-base leading-7 text-slate-600 md:text-lg">{{ $campaign->short_description }}</p>
                    @endif
                </div>

                @if ($featuredImage)
                    <div class="overflow-hidden rounded-[28px]">
                        <img src="{{ $featuredImage }}" alt="{{ $campaign->title }}" class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="prose prose-slate max-w-none">
                    {!! $campaign->description !!}
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-[32px] border border-white/60 bg-white/95 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                    <div class="mb-4 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><x-public.ui.icon name="chart-column" class="h-4 w-4" /> Progress Program</div>
                    <x-public.ui.progress-bar :percentage="$campaign->progress_percentage" color="#E8242A" />
                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Terkumpul</div>
                            <div class="mt-2 text-lg font-bold text-slate-950">
                                {{ $campaign->goal_type === 'nominal' ? $campaign->getFormattedCollectedAmount() : number_format($campaign->collected_unit) . ' ' . $campaign->unit_label }}
                            </div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Target</div>
                            <div class="mt-2 text-lg font-bold text-slate-950">
                                {{ $campaign->goal_type === 'nominal' ? $campaign->getFormattedGoalAmount() : number_format($campaign->goal_unit ?? 0) . ' ' . $campaign->unit_label }}
                            </div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Donatur Terverifikasi</div>
                            <div class="mt-2 text-lg font-bold text-slate-950">{{ number_format($campaign->donor_count) }}</div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <x-public.ui.button :href="route('campaigns.index')" variant="donation" icon="badge-cent" class="w-full justify-center">
                            Donasi Sekarang
                        </x-public.ui.button>
                    </div>
                </div>

                <div class="rounded-[32px] border border-white/60 bg-white/95 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                    <div class="mb-4 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><x-public.ui.icon name="qr-code" class="h-4 w-4" /> Instruksi Donasi</div>
                    @if ($instructionText)
                        <p class="text-sm leading-7 text-slate-600">{{ $instructionText }}</p>
                    @endif

                    @if ($qrisImage)
                        <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white p-3">
                            <img src="{{ $qrisImage }}" alt="QRIS {{ $campaign->title }}" class="w-full rounded-[20px] object-cover">
                        </div>
                    @endif

                    @if ($waNumber)
                        <div class="mt-5">
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNumber) }}"
                                target="_blank"
                                rel="noreferrer"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white"
                            >
                                <x-public.ui.icon name="message-circle" class="h-4 w-4" />
                                Konfirmasi via WhatsApp
                            </a>
                        </div>
                    @endif
                </div>
            </aside>
        </div>

        @if ($latestDistributions->isNotEmpty())
            <div class="space-y-6">
                <x-public.ui.section-header eyebrow="Transparansi" icon="hand-coins" title="Penyaluran Terkait Program" />
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($latestDistributions as $distribution)
                        <x-public.card.distribution-card :distribution="$distribution" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>
