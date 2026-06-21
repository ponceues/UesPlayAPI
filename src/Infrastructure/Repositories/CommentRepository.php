<?php
namespace UesPlay\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use UesPlay\Domain\Entities\Comment;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\ICommentRepository;
use UesPlay\Domain\Mappers\CommentMapper;

class CommentRepository implements  ICommentRepository
{
    private readonly string $table;

    public function __construct()
    {
        $this->table = 'comments';
    }

    public function search(Filter $filter, string $resourceId): Collection
    {
        $query = DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('deleted', false);
        if($filter->getStatus() !== null) {
            $query->where('status', $filter->getStatus());
        }

        $raw = $query->orderBy('created_at', 'desc')
            ->offset($filter->getPage() * $filter->getPageSize())
            ->limit($filter->getPageSize())
            ->get();

        return CommentMapper::fromRawToCollection($raw);
    }
    
    public function fetch(Filter $filter, string $resourceId): Collection
    {
        $query = DB::table($this->table)
        ->where('resource_id', $resourceId)
        ->where('deleted', false);
        
        if ($filter->getStatus() !== null) {
            $query->where('status', $filter->getStatus());
        }
        
        $raw = $query->orderBy('created_at', 'desc')
        ->offset($filter->getPage() * $filter->getPageSize())
        ->limit($filter->getPageSize())
        ->get();
        
        return CommentMapper::fromRawToCollection($raw);
    }

    public function find(string $commentId): Comment
    {
        $raw = DB::table($this->table)
            ->where('comment_id', $commentId)
            ->where('deleted', false)
            ->first();

        return CommentMapper::fromRawToEntity($raw);
    }

    public function count(Filter $filter, string $resourceId): int
    {
        $query = DB::table($this->table)
            ->where('resource_id', $resourceId)
            ->where('deleted', false);
        
            if ($filter->getStatus() !== null) {
                $query->where('status', $filter->getStatus());
            }

        return $query->count();
    }

    public function insert(Comment $comment): Comment
    {
        DB::table($this->table)
            ->insert([
                'comment_id' => $comment->getCommentId(),
                'resource_id' => $comment->getResourceId(),
                'comment' => $comment->getComment(),
                'score' => $comment->getScore(),
                'status' => $comment->getStatus(),
                'commented_by' => $comment->getCommentedBy(),
                'commenters_email' => $comment->getCommentersEmail(),
                'deleted' => false,
                'created_at' => $comment->getCreatedAt(),
                'updated_at' => $comment->getUpdatedAt()
            ]);

        return $this->find($comment->getCommentId());
    }

    public function update(Comment $comment): Comment
    {
        DB::table($this->table)
            ->where('comment_id', $comment->getCommentId())
            ->update([
                'status' => $comment->getStatus(),
                'updated_at' => $comment->getUpdatedAt()
            ]);

        return $this->find($comment->getCommentId());
    }

    public function delete(string $commentId): bool
    {
        DB::table($this->table)
            ->where('comment_id', $commentId)
            ->update([
                'deleted' => true
            ]);

        return true;
    }

}