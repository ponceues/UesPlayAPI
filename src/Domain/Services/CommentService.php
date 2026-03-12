<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Interfaces\ICommentRepository;
use UesPlay\Domain\Entities\Comment;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;

class CommentService
{
    private readonly ICommentRepository $commentRepository;

    public function __construct(ICommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    
    public function search(Filter $filter, string $resourceId): Envelop
    {
        try {
            $res = new Envelop();
            $count = $this->commentRepository->count($filter, $resourceId);
            $list = $this->commentRepository->fetch($filter, $resourceId);
            $res->setData($list, $filter, $count, 'comments');
            return $res;
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function fetch(Filter $filter, string $resourceId): Envelop
    {
        try {
            $res = new Envelop();
            $filter->setStatus('PUBLISHED');
            $count = $this->commentRepository->count($filter, $resourceId);
            $list = $this->commentRepository->fetch($filter, $resourceId);
            $res->setData($list, $filter, $count, 'comments');
            return $res;
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function find(string $commentId): Comment
    {
        try {
            return $this->commentRepository->find($commentId);
        } catch (NotFoundException $ex) {
            throw new NotFoundException('La entidad buscada no existe.');
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function create(Comment $comment): Comment
    {
        try {
            $comment->setCommentId(Uuid::uuid4()->toString());
            $comment->setDeleted(false);
            $comment->setStatus("CREATED");
            $comment->setCreatedAt(Carbon::now('utc'));
            $comment->setUpdatedAt(Carbon::now('utc'));

            $res = $this->commentRepository->insert($comment);
            return $res;
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }
    
    public function update(Comment $comment): Comment
    {
        try {
            $current = $this->commentRepository->find($comment->getCommentId());
            $current->setUpdatedAt(Carbon::now('utc'));
            $current->setStatus($comment->getStatus());
            $res = $this->commentRepository->update($current);
            return $res;
        } catch (BadRequestException $ex) {
            throw $ex;
        } catch (NotFoundException $ex) {
            throw new NotFoundException('La entidad buscada no existe.');
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function delete(string $commentId): bool
    {
        try {
            $this->commentRepository->find($commentId);
            return $this->commentRepository->delete($commentId);
        } catch (NotFoundException $ex) {
            throw new NotFoundException('La entidad buscada no existe.');
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }
}