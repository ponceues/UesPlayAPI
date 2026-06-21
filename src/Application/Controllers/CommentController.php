<?php
namespace UesPlay\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use UesPlay\Domain\Services\CommentService;
use UesPlay\Domain\Mappers\CommentMapper;
use UesPlay\Domain\Mappers\FilterMapper;

class CommentController extends Controller
{
    private readonly CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }
    
    public function listComments(Request $request, string $resourceId): JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        $filter->setStatus('PUBLISHED');
        $env = $this->commentService->search($filter, $resourceId);
        
        return response()->json($env->toArray());
    }

    public function search(Request $request, string $resourceId): JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        $env = $this->commentService->search($filter, $resourceId);
        return response()->json($env->toArray());
    }
    
    public function fetch(Request $request, string $resourceId): JsonResponse
    {
        $filter = FilterMapper::fromRequestToEntity($request);
        $env = $this->commentService->fetch($filter, $resourceId);
        return response()->json($env->toArray());
    }

    public function create(Request $request): JsonResponse
    {
        $comment = CommentMapper::fromRequestToEntity($request, false);
        $res = $this->commentService->create($comment);
        return response()->json($res->toArray());
    }

    public function update(Request $request, string $commentId): JsonResponse
    {
        $comment = CommentMapper::fromRequestToEntity($request, true);
        $res = $this->commentService->update($comment);
        return response()->json($res->toArray());
    }

    public function delete(string $commentId): JsonResponse
    {
        $this->commentService->delete($commentId);
        return response()->json(['result' => 'true']);
    }
}