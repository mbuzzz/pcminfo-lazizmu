<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription" :page-image="$pageImage">
    @php
        $shareUrl = route('posts.show', $post);
        $shareTitle = $post->title;
        $reactionTypes = [
            ['key' => 'like', 'label' => 'Suka', 'icon' => 'thumbs-up'],
            ['key' => 'love', 'label' => 'Love', 'icon' => 'heart'],
            ['key' => 'insightful', 'label' => 'Mencerahkan', 'icon' => 'sparkles'],
            ['key' => 'support', 'label' => 'Dukungan', 'icon' => 'hand-heart'],
        ];
    @endphp
    <article class="mx-auto max-w-4xl px-4 py-8 md:px-6 md:py-12">
        <div class="mb-6">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke berita</span>
            </a>
        </div>
        <div class="space-y-6 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
            <div class="space-y-3">
                <div class="flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    @if ($post->category)
                        <a href="{{ route('posts.categories.show', $post->category) }}" class="inline-flex items-center gap-1.5 transition hover:text-slate-700">
                            <x-public.ui.icon name="bookmark" class="h-3.5 w-3.5" />
                            <span>{{ $post->category->name }}</span>
                        </a>
                    @endif
                    @if ($post->published_at)
                        <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="calendar-days" class="h-3.5 w-3.5" /> {{ $post->published_at->translatedFormat('d F Y') }}</span>
                    @endif
                    @if ($post->institution)
                        <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="building-2" class="h-3.5 w-3.5" /> {{ $post->institution->name }}</span>
                    @endif
                    <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="eye" class="h-3.5 w-3.5" /> {{ number_format($post->view_count) }} dibaca</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $post->title }}</h1>
                @if ($post->excerpt)
                    <p class="text-base leading-7 text-slate-600 md:text-lg">{{ $post->excerpt }}</p>
                @endif
            </div>

            @if ($post->featured_image_url)
                <div class="overflow-hidden rounded-[28px]">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                </div>
            @endif

            <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-[var(--site-primary)] prose-img:rounded-[24px]">
                {!! $post->content !!}
            </div>

            <div
                class="space-y-6 border-t border-slate-200 pt-6"
                x-data="postEngagement({ postSlug: @js($post->slug), allowComments: @js($post->allow_comments) })"
                x-init="init()"
            >
                @php
                    $shareUrl = urlencode(url()->current());
                    $shareTitle = urlencode($post->title);
                @endphp

                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Bagikan</span>
                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                            target="_blank"
                            rel="noreferrer"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950"
                        >Facebook</a>
                        <a
                            href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
                            target="_blank"
                            rel="noreferrer"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950"
                        >Twitter</a>
                        <a
                            href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                            target="_blank"
                            rel="noreferrer"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950"
                        >WhatsApp</a>
                        <a
                            href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                            target="_blank"
                            rel="noreferrer"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950"
                        >LinkedIn</a>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-950"
                        x-on:click="copyLink()"
                    >
                        <span x-text="copyLabel"></span>
                    </button>
                </div>

                <div class="grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="rounded-[28px] border border-slate-200 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Reaksi</div>
                                <div class="mt-1 text-sm font-bold text-slate-950" x-text="reactionSummary"></div>
                            </div>
                            <div class="text-xs font-semibold text-slate-500" x-show="loadingReactions">Memuat…</div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <template x-for="(label, type) in reactionTypes" :key="type">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full border px-3 py-2 text-xs font-semibold transition"
                                    :class="currentReaction === type ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:text-slate-950'"
                                    x-on:click="toggleReaction(type)"
                                >
                                    <span x-text="label"></span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700" x-text="counts[type] ?? 0"></span>
                                </button>
                            </template>
                        </div>

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-xs text-slate-600" x-show="reactionError" x-text="reactionError"></div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Komentar</div>
                                <div class="mt-1 text-sm font-bold text-slate-950" x-text="commentSummary"></div>
                            </div>
                            <div class="text-xs font-semibold text-slate-500" x-show="loadingComments">Memuat…</div>
                        </div>

                        <template x-if="! canComment">
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                                Masuk untuk berkomentar.
                                <a href="{{ route('filament.admin.auth.login') }}" class="font-semibold text-[var(--site-primary)] underline">Login</a>
                            </div>
                        </template>

                        <template x-if="canComment">
                            <form class="mt-4 space-y-3" x-on:submit.prevent="submitComment()">
                                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                    <textarea
                                        rows="3"
                                        class="w-full resize-none bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                                        placeholder="Tulis komentar Anda…"
                                        x-model="body"
                                    ></textarea>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="text-xs text-slate-500" x-show="replyToId">
                                        Membalas komentar <span class="font-semibold" x-text="replyToId"></span>
                                        <button type="button" class="ml-2 font-semibold text-slate-700 underline" x-on:click="cancelReply()">Batal</button>
                                    </div>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-2.5 text-xs font-semibold text-white transition hover:-translate-y-0.5"
                                        :disabled="submitting"
                                    >
                                        <span x-text="submitting ? 'Mengirim…' : 'Kirim komentar'"></span>
                                    </button>
                                </div>
                                <div class="rounded-2xl bg-red-50 p-4 text-sm text-red-700" x-show="commentError" x-text="commentError"></div>
                            </form>
                        </template>

                        <div class="mt-6 space-y-4" x-show="comments.length">
                            <template x-for="comment in comments" :key="comment.id">
                                <div class="rounded-[22px] border border-slate-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-slate-950" x-text="comment.author?.name ?? 'Pengunjung'"></div>
                                            <div class="mt-1 text-xs text-slate-500" x-text="formatDate(comment.created_at, comment.edited_at)"></div>
                                        </div>
                                        <div class="flex items-center gap-2" x-show="comment.permissions?.can_update || comment.permissions?.can_delete">
                                            <button type="button" class="text-xs font-semibold text-slate-600 underline" x-show="comment.permissions?.can_update" x-on:click="startEdit(comment)">Ubah</button>
                                            <button type="button" class="text-xs font-semibold text-red-600 underline" x-show="comment.permissions?.can_delete" x-on:click="deleteComment(comment.id)">Hapus</button>
                                        </div>
                                    </div>

                                    <template x-if="editingId !== comment.id">
                                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700" x-text="comment.body"></p>
                                    </template>

                                    <template x-if="editingId === comment.id">
                                        <div class="mt-3 space-y-3">
                                            <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                                <textarea rows="3" class="w-full resize-none bg-transparent text-sm text-slate-900 focus:outline-none" x-model="editingBody"></textarea>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <button type="button" class="rounded-full bg-slate-950 px-4 py-2 text-xs font-semibold text-white" x-on:click="saveEdit(comment.id)">Simpan</button>
                                                <button type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700" x-on:click="cancelEdit()">Batal</button>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                                        <button
                                            type="button"
                                            class="text-xs font-semibold text-[var(--site-primary)] underline"
                                            x-show="canComment"
                                            x-on:click="replyTo(comment.id)"
                                        >Balas</button>
                                        <div class="text-xs text-slate-500" x-show="comment.replies?.length" x-text="comment.replies.length + ' balasan'"></div>
                                    </div>

                                    <div class="mt-4 space-y-3" x-show="comment.replies?.length">
                                        <template x-for="reply in (comment.replies ?? [])" :key="reply.id">
                                            <div class="rounded-2xl bg-slate-50 p-3">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-bold text-slate-900" x-text="reply.author?.name ?? 'Pengunjung'"></div>
                                                        <div class="mt-1 text-xs text-slate-500" x-text="formatDate(reply.created_at, reply.edited_at)"></div>
                                                    </div>
                                                    <div class="flex items-center gap-2" x-show="reply.permissions?.can_update || reply.permissions?.can_delete">
                                                        <button type="button" class="text-xs font-semibold text-slate-600 underline" x-show="reply.permissions?.can_update" x-on:click="startEdit(reply)">Ubah</button>
                                                        <button type="button" class="text-xs font-semibold text-red-600 underline" x-show="reply.permissions?.can_delete" x-on:click="deleteComment(reply.id)">Hapus</button>
                                                    </div>
                                                </div>
                                                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700" x-text="reply.body"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700" x-show="! loadingComments && comments.length === 0">
                            Belum ada komentar. Jadilah yang pertama.
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 border-t border-slate-200 pt-5 text-sm text-slate-600 md:grid-cols-2">
                @if ($post->author)
                    <div class="inline-flex items-center gap-2"><x-public.ui.icon name="user-round" class="h-4 w-4" /> Penulis: <span class="font-semibold text-slate-900">{{ $post->author->name }}</span></div>
                @endif
                @if ($post->institution)
                    <div class="inline-flex items-center gap-2"><x-public.ui.icon name="building-2" class="h-4 w-4" /> Terkait: <span class="font-semibold text-slate-900">{{ $post->institution->name }}</span></div>
                @endif
            </div>

            <section
                x-data="postEngagement({
                    commentsEndpoint: @js(url('/api/posts/' . $post->slug . '/comments')),
                    reactionsEndpoint: @js(url('/api/posts/' . $post->slug . '/reactions')),
                    loginUrl: @js(url('/admin/login')),
                    canComment: @js((bool) $post->allow_comments),
                    isAuthenticated: @js(auth()->check()),
                    initialCommentCount: @js((int) $post->comments_count),
                    initialReactionCount: @js((int) $post->reactions_count),
                })"
                x-init="init()"
                class="space-y-6 border-t border-slate-200 pt-6"
            >
                <div class="grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="rounded-[28px] border border-slate-200 bg-slate-50/90 p-5">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500 shadow-sm">
                            <x-public.ui.icon name="share-2" class="h-3.5 w-3.5" />
                            Bagikan Artikel
                        </div>
                        <h2 class="mt-4 text-xl font-black tracking-tight text-slate-950">Sebarkan informasi yang bermanfaat</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-600">Bagikan artikel ini ke jaringan Anda agar informasi organisasi dan gerakan bisa menjangkau lebih banyak orang.</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                                <x-public.ui.icon name="facebook" class="h-4 w-4" />
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                                <x-public.ui.icon name="twitter" class="h-4 w-4" />
                                Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                                <x-public.ui.icon name="message-circle" class="h-4 w-4" />
                                WhatsApp
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-950">
                                <x-public.ui.icon name="linkedin" class="h-4 w-4" />
                                LinkedIn
                            </a>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_10px_26px_rgba(15,23,42,0.06)]">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">
                                    <x-public.ui.icon name="sparkles" class="h-3.5 w-3.5" />
                                    Reaksi Pembaca
                                </div>
                                <h2 class="mt-4 text-xl font-black tracking-tight text-slate-950">Bagaimana menurut Anda?</h2>
                            </div>
                            <div class="rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white">
                                <span x-text="reactionTotal"></span> reaksi
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach ($reactionTypes as $reactionType)
                                <button
                                    type="button"
                                    x-on:click="toggleReaction('{{ $reactionType['key'] }}')"
                                    :class="currentReaction === '{{ $reactionType['key'] }}' ? 'border-slate-950 bg-slate-950 text-white shadow-sm' : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-slate-300 hover:bg-white'"
                                    class="flex items-center justify-between rounded-[22px] border px-4 py-3 text-left transition"
                                >
                                    <span class="inline-flex items-center gap-2 text-sm font-semibold">
                                        <x-public.ui.icon name="{{ $reactionType['icon'] }}" class="h-4 w-4" />
                                        {{ $reactionType['label'] }}
                                    </span>
                                    <span class="text-sm font-bold" x-text="reactionCounts['{{ $reactionType['key'] }}'] ?? 0"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_10px_26px_rgba(15,23,42,0.06)]">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">
                                <x-public.ui.icon name="messages-square" class="h-3.5 w-3.5" />
                                Komentar
                            </div>
                            <h2 class="mt-4 text-xl font-black tracking-tight text-slate-950">Diskusi Pembaca</h2>
                            <p class="mt-2 text-sm leading-7 text-slate-600">Komentar ditautkan ke akun pengguna yang sedang login agar diskusi tetap rapi dan bertanggung jawab.</p>
                        </div>
                        <div class="rounded-full bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                            <span x-text="comments.length || initialCommentCount"></span> komentar
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        @if ($post->allow_comments)
                            @auth
                                <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                                    <label class="mb-3 block text-sm font-semibold text-slate-900">Tulis komentar Anda</label>
                                    <textarea x-model="commentBody" rows="4" class="w-full rounded-[20px] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-300 focus:outline-none" placeholder="Bagikan tanggapan Anda terhadap artikel ini..."></textarea>
                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-xs text-slate-500" x-text="feedbackMessage"></p>
                                        <button type="button" x-on:click="submitComment()" class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                            <x-public.ui.icon name="send" class="h-4 w-4" />
                                            Kirim komentar
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                                    Masuk terlebih dahulu untuk memberi reaksi atau komentar.
                                    <a href="{{ url('/admin/login') }}" class="ml-1 font-semibold text-[var(--site-primary)]">Masuk sekarang</a>
                                </div>
                            @endauth
                        @else
                            <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                                Komentar dinonaktifkan untuk artikel ini.
                            </div>
                        @endif

                        <template x-if="comments.length === 0">
                            <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-sm text-slate-500">
                                Belum ada komentar. Jadilah yang pertama memulai diskusi.
                            </div>
                        </template>

                        <div class="space-y-4">
                            <template x-for="comment in comments" :key="comment.id">
                                <div class="rounded-[24px] border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3">
                                            <div class="h-11 w-11 overflow-hidden rounded-full bg-slate-100">
                                                <template x-if="comment.author.avatar_url">
                                                    <img :src="comment.author.avatar_url" :alt="comment.author.name" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!comment.author.avatar_url">
                                                    <div class="flex h-full w-full items-center justify-center text-sm font-bold text-slate-400" x-text="comment.author.name?.slice(0, 1) ?? '?'"></div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-950" x-text="comment.author.name"></div>
                                                <div class="text-xs text-slate-500" x-text="formatDate(comment.created_at)"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2" x-show="comment.permissions.can_update || comment.permissions.can_delete">
                                            <button type="button" x-show="comment.permissions.can_update" x-on:click="startEditing(comment)" class="text-xs font-semibold text-slate-500 transition hover:text-slate-900">Ubah</button>
                                            <button type="button" x-show="comment.permissions.can_delete" x-on:click="deleteComment(comment.id)" class="text-xs font-semibold text-red-500 transition hover:text-red-600">Hapus</button>
                                        </div>
                                    </div>

                                    <template x-if="editingId === comment.id">
                                        <div class="mt-4 space-y-3">
                                            <textarea x-model="editingBody" rows="4" class="w-full rounded-[18px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-slate-300 focus:outline-none"></textarea>
                                            <div class="flex flex-wrap gap-2">
                                                <button type="button" x-on:click="updateComment(comment.id)" class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
                                                <button type="button" x-on:click="cancelEditing()" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Batal</button>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="editingId !== comment.id">
                                        <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700" x-text="comment.body"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>
            </section>
        </div>

        @if ($relatedPosts->isNotEmpty())
            <div class="mt-12 space-y-6">
                <x-public.ui.section-header eyebrow="Artikel Terkait" icon="newspaper" title="Baca Juga" />
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($relatedPosts as $relatedPost)
                        <x-public.card.news-card :post="$relatedPost" />
                    @endforeach
                </div>
            </div>
        @endif
    </article>

    <script>
        window.postEngagement = function (config) {
            return {
                comments: [],
                commentBody: '',
                editingId: null,
                editingBody: '',
                feedbackMessage: '',
                reactionCounts: { like: 0, love: 0, insightful: 0, support: 0 },
                reactionTotal: config.initialReactionCount ?? 0,
                currentReaction: null,
                initialCommentCount: config.initialCommentCount ?? 0,
                async init() {
                    await Promise.all([this.fetchComments(), this.fetchReactions()]);
                },
                csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
                },
                async fetchComments() {
                    const response = await fetch(config.commentsEndpoint, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    this.comments = payload.data ?? [];
                },
                async fetchReactions() {
                    const response = await fetch(config.reactionsEndpoint, {
                        headers: { Accept: 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    this.reactionCounts = payload.data?.counts ?? this.reactionCounts;
                    this.reactionTotal = payload.data?.total ?? 0;
                    this.currentReaction = payload.data?.current_user_reaction ?? null;
                },
                async submitComment() {
                    if (!config.isAuthenticated) {
                        window.location.href = config.loginUrl;
                        return;
                    }

                    if (!this.commentBody.trim()) {
                        this.feedbackMessage = 'Komentar tidak boleh kosong.';
                        return;
                    }

                    const response = await fetch(config.commentsEndpoint, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                        body: JSON.stringify({ body: this.commentBody }),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        this.feedbackMessage = payload.message ?? 'Komentar gagal dikirim.';
                        return;
                    }

                    this.comments.unshift(payload.data);
                    this.commentBody = '';
                    this.feedbackMessage = payload.message ?? 'Komentar berhasil ditambahkan.';
                },
                startEditing(comment) {
                    this.editingId = comment.id;
                    this.editingBody = comment.body;
                },
                cancelEditing() {
                    this.editingId = null;
                    this.editingBody = '';
                },
                async updateComment(commentId) {
                    const response = await fetch(`/api/comments/${commentId}`, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                        body: JSON.stringify({ body: this.editingBody }),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        this.feedbackMessage = payload.message ?? 'Komentar gagal diperbarui.';
                        return;
                    }

                    this.comments = this.comments.map(comment => comment.id === commentId ? payload.data : comment);
                    this.cancelEditing();
                    this.feedbackMessage = payload.message ?? 'Komentar berhasil diperbarui.';
                },
                async deleteComment(commentId) {
                    const response = await fetch(`/api/comments/${commentId}`, {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                    });

                    if (!response.ok) {
                        this.feedbackMessage = 'Komentar gagal dihapus.';
                        return;
                    }

                    this.comments = this.comments.filter(comment => comment.id !== commentId);
                    this.feedbackMessage = 'Komentar berhasil dihapus.';
                },
                async toggleReaction(type) {
                    if (!config.isAuthenticated) {
                        window.location.href = config.loginUrl;
                        return;
                    }

                    const response = await fetch(config.reactionsEndpoint, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                        body: JSON.stringify({ type }),
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    this.reactionCounts = payload.data?.counts ?? this.reactionCounts;
                    this.reactionTotal = payload.data?.total ?? this.reactionTotal;
                    this.currentReaction = payload.data?.current_user_reaction ?? null;
                },
                formatDate(value) {
                    if (!value) {
                        return '';
                    }

                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }).format(new Date(value));
                },
            };
        };
    </script>
</x-layouts.public>

@push('scripts')
    <script>
        function postEngagement({ postSlug, allowComments }) {
            return {
                postSlug,
                allowComments,
                reactionTypes: {
                    like: 'Suka',
                    love: 'Love',
                    insightful: 'Mencerahkan',
                    support: 'Dukungan',
                },
                loadingComments: true,
                loadingReactions: true,
                canComment: false,
                comments: [],
                commentCount: 0,
                counts: {},
                totalReactions: 0,
                currentReaction: null,
                body: '',
                replyToId: null,
                submitting: false,
                commentError: null,
                reactionError: null,
                copyLabel: 'Salin tautan',
                editingId: null,
                editingBody: '',
                get csrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                },
                get reactionSummary() {
                    return this.totalReactions ? `${this.totalReactions} reaksi` : 'Belum ada reaksi.';
                },
                get commentSummary() {
                    return this.commentCount ? `${this.commentCount} komentar` : 'Belum ada komentar.';
                },
                async init() {
                    await Promise.all([this.loadReactions(), this.loadComments()]);
                },
                async loadComments() {
                    this.loadingComments = true;
                    this.commentError = null;

                    try {
                        const response = await fetch(`/api/posts/${this.postSlug}/comments`, {
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat komentar.');
                        }

                        const payload = await response.json();
                        this.comments = Array.isArray(payload.data) ? payload.data : [];
                        this.commentCount = payload.meta?.count ?? this.comments.length;
                        this.canComment = Boolean(payload.meta?.can_comment) && Boolean(this.allowComments);
                    } catch (error) {
                        this.commentError = error?.message ?? 'Terjadi kesalahan saat memuat komentar.';
                    } finally {
                        this.loadingComments = false;
                    }
                },
                async loadReactions() {
                    this.loadingReactions = true;
                    this.reactionError = null;

                    try {
                        const response = await fetch(`/api/posts/${this.postSlug}/reactions`, {
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Gagal memuat reaksi.');
                        }

                        const payload = await response.json();
                        this.counts = payload.data?.counts ?? {};
                        this.totalReactions = payload.data?.total ?? 0;
                        this.currentReaction = payload.data?.current_user_reaction ?? null;
                    } catch (error) {
                        this.reactionError = error?.message ?? 'Terjadi kesalahan saat memuat reaksi.';
                    } finally {
                        this.loadingReactions = false;
                    }
                },
                async toggleReaction(type) {
                    this.reactionError = null;

                    try {
                        const response = await fetch(`/api/posts/${this.postSlug}/reactions`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({ type }),
                        });

                        if (response.status === 401) {
                            window.location.href = '{{ route('filament.admin.auth.login') }}';
                            return;
                        }

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            throw new Error(payload?.message ?? 'Gagal memperbarui reaksi.');
                        }

                        const payload = await response.json();
                        this.counts = payload.data?.counts ?? this.counts;
                        this.totalReactions = payload.data?.total ?? this.totalReactions;
                        this.currentReaction = payload.data?.current_user_reaction ?? this.currentReaction;
                    } catch (error) {
                        this.reactionError = error?.message ?? 'Terjadi kesalahan saat memperbarui reaksi.';
                    }
                },
                replyTo(commentId) {
                    this.replyToId = commentId;
                    this.$nextTick(() => {
                        const textarea = this.$root.querySelector('textarea');
                        textarea?.focus();
                    });
                },
                cancelReply() {
                    this.replyToId = null;
                },
                startEdit(comment) {
                    this.editingId = comment.id;
                    this.editingBody = comment.body;
                },
                cancelEdit() {
                    this.editingId = null;
                    this.editingBody = '';
                },
                async saveEdit(commentId) {
                    this.commentError = null;

                    try {
                        const response = await fetch(`/api/comments/${commentId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({ body: this.editingBody }),
                        });

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            throw new Error(payload?.message ?? 'Gagal memperbarui komentar.');
                        }

                        this.cancelEdit();
                        await this.loadComments();
                    } catch (error) {
                        this.commentError = error?.message ?? 'Terjadi kesalahan saat memperbarui komentar.';
                    }
                },
                async deleteComment(commentId) {
                    this.commentError = null;

                    try {
                        const response = await fetch(`/api/comments/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                        });

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            throw new Error(payload?.message ?? 'Gagal menghapus komentar.');
                        }

                        await this.loadComments();
                    } catch (error) {
                        this.commentError = error?.message ?? 'Terjadi kesalahan saat menghapus komentar.';
                    }
                },
                async submitComment() {
                    if (!this.body.trim()) {
                        this.commentError = 'Komentar tidak boleh kosong.';
                        return;
                    }

                    this.commentError = null;
                    this.submitting = true;

                    try {
                        const response = await fetch(`/api/posts/${this.postSlug}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                body: this.body,
                                parent_id: this.replyToId,
                            }),
                        });

                        if (response.status === 401) {
                            window.location.href = '{{ route('filament.admin.auth.login') }}';
                            return;
                        }

                        if (!response.ok) {
                            const payload = await response.json().catch(() => null);
                            throw new Error(payload?.message ?? 'Gagal mengirim komentar.');
                        }

                        this.body = '';
                        this.replyToId = null;
                        await this.loadComments();
                    } catch (error) {
                        this.commentError = error?.message ?? 'Terjadi kesalahan saat mengirim komentar.';
                    } finally {
                        this.submitting = false;
                    }
                },
                formatDate(createdAt, editedAt) {
                    if (!createdAt) {
                        return '';
                    }

                    const date = new Date(createdAt);
                    const formatted = date.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    return editedAt ? `${formatted} (diedit)` : formatted;
                },
                async copyLink() {
                    const url = window.location.href;

                    try {
                        await navigator.clipboard.writeText(url);
                        this.copyLabel = 'Tersalin';
                    } catch (error) {
                        this.copyLabel = 'Gagal menyalin';
                    }

                    window.setTimeout(() => {
                        this.copyLabel = 'Salin tautan';
                    }, 2000);
                },
            };
        }
    </script>
@endpush
