<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Entities\MediaGenre;
use UesPlay\Domain\Interfaces\IMediaGenreRepository;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;

class MediaGenreService
{
    private readonly IMediaGenreRepository $mediaGenreRepository;

    public function __construct(IMediaGenreRepository $mediaGenreRepository) {
        $this->mediaGenreRepository = $mediaGenreRepository;
    }
    
    public function listGenres(Filter $filter): Envelop {
        try {
            $envelop = new Envelop();
            $filter->setEnabled(true);
            
            $data = $this->mediaGenreRepository->search($filter);
            $count = $this->mediaGenreRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'mediaGenres');
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function search(Filter $filter, string $mediaTypeId): Envelop {
        try {
            $envelop = new Envelop();
            $filter->setMediaTypeId($mediaTypeId);
            $data = $this->mediaGenreRepository->search($filter);
            $count = $this->mediaGenreRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'mediaGenres');
            return $envelop;
        } catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function fetch(Filter $filter): Envelop {
        try {
            $envelop = new Envelop();
            $filter->setEnabled(true);
            $data = $this->mediaGenreRepository->search($filter);
            $count = $this->mediaGenreRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'mediaGenres');
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    
    public function create(MediaGenre $mediaGenre): MediaGenre {
        try {
            $exists = $this->mediaGenreRepository->findByName($mediaGenre->getName(), null);
            if ($exists != null) {
                throw new BadRequestException("Ya existe un género con el nombre " . $mediaGenre->getName());
            }
            $mediaGenre->setGenreId(Uuid::uuid4()->toString());
            $mediaGenre->setCreatedAt(Carbon::now('utc'));
            $mediaGenre->setUpdatedAt(Carbon::now('utc'));
            $res = $this->mediaGenreRepository->insert($mediaGenre);
            return $res;
        } catch (BadRequestException $bre) {
            throw $bre;
        } catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function update(MediaGenre $mediaGenre): MediaGenre {
        try {
            $exists = $this->mediaGenreRepository->findByName($mediaGenre->getName(), $mediaGenre->getGenreId());
            if ($exists != null) {
                throw new BadRequestException("Ya existe otro género con el nombre " . $mediaGenre->getName());
            }
            $current = $this->mediaGenreRepository->find($mediaGenre->getGenreId());
            $current->setName($mediaGenre->getName());
            $current->setDescription($mediaGenre->getDescription());
            $current->setEnabled($mediaGenre->isEnabled());
            $current->setUpdatedAt(Carbon::now('utc'));
            $res = $this->mediaGenreRepository->update($current);
            return $res;
        } catch (BadRequestException $bre) {
            throw $bre;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }

    public function trash(string $mediaGenreId): array {
        try {
            $current = $this->mediaGenreRepository->find($mediaGenreId);
            $res = $this->mediaGenreRepository->delete($current->getGenreId());
            return ['result' => $res];
        } catch (NotFoundException $bre) {
            throw $bre;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}