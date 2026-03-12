<?php
namespace UesPlay\Domain\Mappers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Entities\Comment;

class CommentMapper
{
    public static function fromRawToEntity($raw): Comment {
        if($raw === null){
            throw new NotFoundException('La entidad {Comment} no existe');
        }

        $entity = new Comment();
        $entity->setCommentId($raw->comment_id);
        $entity->setResourceId($raw->resource_id);
        $entity->setComment($raw->comment);
        $entity->setScore((int)$raw->score);
        $entity->setStatus($raw->status);
        $entity->setCommentedBy($raw->commented_by);
        $entity->setCommentersEmail($raw->commenters_email);
        $entity->setDeleted((bool)$raw->deleted);
        $entity->setCreatedAt(new DateTime($raw->created_at));
        $entity->setUpdatedAt(new DateTime($raw->updated_at));

        return $entity;
    }

    public static function fromRawToCollection($raw): Collection {
        $list = collect();
        foreach($raw as $item){
            $list->push(CommentMapper::fromRawToEntity($item));
        }
        return $list;
    }

    public static function fromRequestToEntity(Request $request, bool $isUpdate): Comment {
        $entity = new Comment();

        $validate = Validator::make(
            $request->all(),
            [
                'commentId' => [
                    'nullable',
                    'uuid',
                    Rule::requiredIf($isUpdate)
                ],
                'resourceId' => 'required|uuid',
                'comment' => 'required|string|min:1',
                'score' => 'required|integer|min:0|max:5',
                'status' => [
                    Rule::requiredIf($isUpdate),
                    'string',
                    'in:PUBLISHED,REJECTED',
                ],
                
                'commentedBy' => 'required|string',
                'commentersEmail' => 'required|email:rfc,dns',
            ],
            [
                'commentId.required' => 'El campo commentId es obligatorio para la actualización.',
                'resourceId.required' => 'El campo resourceId es obligatorio.',
                'comment.required' => 'El campo comment es obligatorio.',
                'score.required' => 'El campo score es obligatorio.',
                'status.required' => 'El campo status es obligatorio.',
                'commentedBy.required' => 'El campo commentedBy es obligatorio.',
                'commentersEmail.required' => 'El campo commentersEmail es obligatorio.',
                
            ]
        );

        if($validate->fails()){
            throw new BadRequestException($validate->errors()->first());
        }

        if($isUpdate){
            $entity->setCommentId($request->string('commentId'));
        }

        $entity->setResourceId($request->string('resourceId'));
        $entity->setComment($request->input('comment'));
        $entity->setScore($request->has('score') ? (int)$request->input('score') : 0);
        $entity->setStatus($request->has('status') ? $request->input('status') : '');
        $entity->setCommentedBy($request->has('commentedBy') ? $request->input('commentedBy') : '');
        $entity->setCommentersEmail($request->has('commentersEmail') ? $request->input('commentersEmail') : '');

        return $entity;
    }
}