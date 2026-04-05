<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\PostReactionType;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostReactionController extends Controller
{
    public function show(Post $post): JsonResponse
    {
        abort_unless($post->isPublished(), Response::HTTP_NOT_FOUND);

        return response()->json($this->reactionPayload($post));
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        abort_unless($post->isPublished(), Response::HTTP_NOT_FOUND);

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', PostReactionType::values())],
        ]);

        $reaction = PostReaction::query()->firstOrNew([
            'post_id' => $post->getKey(),
            'user_id' => $request->user()->getKey(),
        ]);

        $selectedType = PostReactionType::from($validated['type']);

        if ($reaction->exists && $reaction->type === $selectedType) {
            $reaction->delete();
        } else {
            $reaction->type = $selectedType;
            $reaction->save();
        }

        return response()->json([
            'message' => 'Reaksi berhasil diperbarui.',
            ...$this->reactionPayload($post->fresh()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function reactionPayload(Post $post): array
    {
        $counts = PostReaction::query()
            ->where('post_id', $post->getKey())
            ->selectRaw('type, count(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');

        return [
            'data' => [
                'counts' => collect(PostReactionType::cases())
                    ->mapWithKeys(fn (PostReactionType $type): array => [$type->value => (int) ($counts[$type->value] ?? 0)])
                    ->all(),
                'total' => (int) $counts->sum(),
                'current_user_reaction' => auth()->check()
                    ? PostReaction::query()
                        ->where('post_id', $post->getKey())
                        ->where('user_id', auth()->id())
                        ->value('type')
                    : null,
            ],
        ];
    }
}
