<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Comments;

use App\Factory\CreateCommentDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\Collab\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService,
        private CreateCommentDtoFactory $dtoFactory
    ) {}

    public function index(Request $request): JsonResponse
    {
        $commentableType = $request->get('commentable_type');
        $commentableId = $request->get('commentable_id');
        $perPage = (int) $request->get('per_page', 15);

        if (! $commentableType || ! $commentableId) {
            return response()->json(['error' => 'commentable_type and commentable_id are required'], 400);
        }

        $comments = $this->commentService->getComments(
            $commentableType,
            $commentableId,
            Auth::user()->id,
            $perPage
        );

        return response()->json($comments);
    }

    public function store(CreateCommentRequest $request): JsonResponse
    {
        $user = Auth::user();
        $dto = $this->dtoFactory->fromArray($request->validated());

        $commentId = $this->commentService->createComment(
            $dto->content(),
            $dto->commentableType(),
            $dto->commentableId(),
            $user->id,
            $dto->parentId(),
            $dto->isPrivate(),
        );

        $comment = $this->commentService->getCommentById($commentId);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $comment = $this->commentService->getCommentById($id);

        if (! $comment) {
            abort(404, 'Comment not found.');
        }

        return response()->json(new CommentResource($comment));
    }

    public function update(CreateCommentRequest $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $dto = $this->dtoFactory->fromArray($request->validated());

        try {
            $this->commentService->updateComment($id, $dto->content(), $user->id);

            $comment = $this->commentService->getCommentById($id);

            return response()->json(new CommentResource($comment));
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                abort(404, 'Comment not found');
            }
            if (str_contains($e->getMessage(), 'only edit your own')) {
                abort(403, 'You can only edit your own comments');
            }

            throw $e;
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $this->commentService->deleteComment($id, $user->id);

            return response()->json(['message' => 'Comment deleted successfully.']);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), 'not found')) {
                abort(404, 'Comment not found');
            }
            if (str_contains($e->getMessage(), 'only delete your own')) {
                abort(403, 'You can only delete your own comments');
            }

            throw $e;
        }
    }

    public function replies(string $id, Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);

        $replies = $this->commentService->getReplies($id, $perPage);

        return response()->json($replies);
    }
}
