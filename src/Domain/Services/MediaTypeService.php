<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;
use Ramsey\Uuid\Uuid;
use UesPlay\Domain\Interfaces\IMediaTypeRepository;
use UesPlay\Domain\Entities\MediaType;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;

class MediaTypeService
{
    private readonly IMediaTypeRepository $mediaTypeRepository;

    public function __construct(IMediaTypeRepository $mediaTypeRepository) {
        $this->mediaTypeRepository = $mediaTypeRepository;
    }

    public function search(Filter $filter): Envelop {
        try {
            $envelop = new Envelop();
            $data = $this->mediaTypeRepository->search($filter);
            $count = $this->mediaTypeRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'mediaTypes');
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function fetch(Filter $filter): Envelop {
        try {
            $envelop = new Envelop();
            $filter->setEnabled(true);
            $data = $this->mediaTypeRepository->search($filter);
            $count = $this->mediaTypeRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'mediaTypes');
            
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function find(string $mediaTypeId): MediaType {
        try {
            $res = $this->mediaTypeRepository->find($mediaTypeId);
            return $res;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    
    public function create(MediaType $mediaType): MediaType {
        try {
            $exists = $this->mediaTypeRepository->findByName($mediaType->getName(), null);
            if ($exists != null) {
                throw new BadRequestException("Ya existe un tipo de medio con el nombre " . $mediaType->getName());
            }
            $mediaType->setTypeId(Uuid::uuid4()->toString());
            $mediaType->setCreatedAt(Carbon::now('utc'));
            $mediaType->setUpdatedAt(Carbon::now('utc'));
            $res = $this->mediaTypeRepository->insert($mediaType);
            return $res;
        } catch (BadRequestException $bre) {
            throw $bre;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function update(MediaType $mediaType): MediaType {
        try {
            $exists = $this->mediaTypeRepository->findByName($mediaType->getName(), $mediaType->getTypeId());
            if ($exists != null) {
                throw new BadRequestException("Ya existe otro tipo de medio con el nombre " . $mediaType->getName());
            }
            $current = $this->mediaTypeRepository->find($mediaType->getTypeId());
            $current->setName($mediaType->getName());
            $current->setDescription($mediaType->getDescription());
            $current->setEnabled($mediaType->isEnabled());
            $current->setUpdatedAt(Carbon::now('utc'));
            $res = $this->mediaTypeRepository->update($current);
            return $res;
        } catch (BadRequestException $bre) {
            throw $bre;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function delete(string $typeId): array {
        try {
            $current = $this->mediaTypeRepository->find($typeId);
            $res = $this->mediaTypeRepository->delete($current->getTypeId());
            return ['result' => $res];
        } catch (NotFoundException $ex) {
            throw $ex;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}