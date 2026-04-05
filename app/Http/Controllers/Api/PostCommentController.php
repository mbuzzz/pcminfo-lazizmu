<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostCommentRequest;
use App\Http\Requests\UpdatePostCommentRequest;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostCommentController extends Controller
{
    public function index(Post $post): JsonResponse
    {
        abort_unless($post->isPublished(), Response::HTTP_NOT_FOUND);

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();

        return response()->json([
            'data' => $comments->map(fn (PostComment $comment): array => $this->serializeComment($comment))->all(),
            'meta' => [
                'count' => $comments->count(),
                'can_comment' => auth()->check() && $post->allow_comments,
            ],
        ]);
    }

    public function store(StorePostCommentRequest $request, Post $post): JsonResponse
    {
        abort_unless($post->isPublished() && $post->allow_comments, Response::HTTP_NOT_FOUND);

        $validated = $request->validated();

        if (filled($validated['parent_id'] ?? null)) {
            $parentComment = PostComment::query()
                ->whereKey($validated['parent_id'])
                ->where('post_id', $post->getKey())
                ->firstOrFail();
        } else {
            $parentComment = null;
        }

        $comment = PostComment::query()->create([
            'post_id' => $post->getKey(),
            'user_id' => $request->user()->getKey(),
            'parent_id' => $parentComment?->getKey(),
            'body' => trim(strip_tags((string) $validated['body'])),
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan.',
            'data' => $this->serializeComment($comment->loadMissing('user', 'replies.user')),
        ], Response::HTTP_CREATED);
    }

    public function show(PostComment $postComment): JsonResponse
    {
        return response()->json([
            'data' => $this->serializeComment($postComment->loadMissing('user', 'replies.user')),
        ]);
    }

    public function update(UpdatePostCommentRequest $request, PostComment $postComment): JsonResponse
    {
        $this->authorizeCommentMutation($postComment);

        $postComment->update([
            'body' => trim(strip_tags((string) $request->validated()['body'])),
            'edited_at' => now(),
        ]);

        return response()->json([
            'message' => 'Komentar berhasil diperbarui.',
            'data' => $this->serializeComment($postComment->fresh(['user', 'replies.user'])),
        ]);
    }

    public function destroy(PostComment $postComment): JsonResponse
    {
        $this->authorizeCommentMutation($postComment);

        $postComment->delete();

        return response()->json([
            'message' => 'Komentar berhasil dihapus.',
        ]);
    }

    private function authorizeCommentMutation(PostComment $postComment): void
    {
        abort_unless(
            auth()->id() === $postComment->user_id || auth()->user()?->can('manage_posts'),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeComment(PostComment $comment): array
    {
        return [
            'id' => $comment->getKey(),
            'body' => $comment->body,
            'edited_at' => $comment->edited_at?->toISOString(),
            'created_at' => $comment->created_at?->toISOString(),
            'author' => [
                'id' => $comment->user?->getKey(),
                'name' => $comment->user?->name,
                'avatar_url' => $comment->user?->avatar_url,
            ],
            'permissions' => [
                'can_update' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()?->can('manage_posts')),
                'can_delete' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()?->can('manage_posts')),
            ],
            'replies' => $comment->replies
                ->map(fn (PostComment $reply): array => [
                    'id' => $reply->getKey(),
                    'body' => $reply->body,
                    'edited_at' => $reply->edited_at?->toISOString(),
                    'created_at' => $reply->created_at?->toISOString(),
                    'author' => [
                        'id' => $reply->user?->getKey(),
                        'name' => $reply->user?->name,
                        'avatar_url' => $reply->user?->avatar_url,
                    ],
                    'permissions' => [
                        'can_update' => auth()->check() && (auth()->id() === $reply->user_id || auth()->user()?->can('manage_posts')),
                        'can_delete' => auth()->check() && (auth()->id() === $reply->user_id || auth()->user()?->can('manage_posts')),
                    ],
                ])
                ->values()
                ->all(),
        ];
    }
}
