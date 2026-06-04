<?php

namespace UesPlay\Domain\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use UesPlay\Domain\Entities\Area;
use UesPlay\Domain\Entities\Author;
use UesPlay\Domain\Entities\Resource;
use UesPlay\Domain\Entities\ResourceFile;
use UesPlay\Domain\Entities\ResourceState;
use UesPlay\Domain\Entities\ResourceType;
use UesPlay\Domain\Entities\Comment;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Interfaces\IAuthorRepository;
use UesPlay\Domain\Interfaces\IDeviceRepository;
use UesPlay\Domain\Interfaces\ILanguageRepository;
use UesPlay\Domain\Interfaces\ILicenseRepository;
use UesPlay\Domain\Interfaces\IPlatformRepository;
use UesPlay\Domain\Interfaces\IResourceAuthorRepository;
use UesPlay\Domain\Interfaces\IResourceFileRepository;
use UesPlay\Domain\Interfaces\IResourceRepository;
use UesPlay\Domain\Interfaces\IResourceStateRepository;
use UesPlay\Domain\Interfaces\IResourceTypeRepository;
use UesPlay\Domain\Interfaces\IUserRepository;
use UesPlay\Domain\Interfaces\IVersionDeviceRepository;
use UesPlay\Domain\Interfaces\IVersionLangsRepository;
use UesPlay\Domain\Interfaces\IVersionPlatformRepository;
use UesPlay\Domain\Interfaces\IVersionRepository;
use UesPlay\Domain\Interfaces\ICommentRepository;
use Exception;

class ResourceService {
    private readonly IResourceRepository $resourceRepository;
    private readonly IResourceStateRepository $resourceStateRepository;
    private readonly IResourceTypeRepository $resourceTypeRepository;
    private readonly IAreaRepository $areaRepository;
    private readonly IAuthorRepository $authorRepository;
    private readonly IResourceAuthorRepository $resourceAuthorRepository;
    private readonly IResourceFileRepository $resourceFileRepository;
    private readonly IUserRepository $userRepository;
    private readonly IVersionRepository $versionRepository;
    private readonly ILanguageRepository $languageRepository;
    private readonly IDeviceRepository $deviceRepository;
    private readonly IPlatformRepository $platformRepository;
    private readonly IVersionDeviceRepository $versionDeviceRepository;
    private readonly IVersionLangsRepository $versionLangsRepository;
    private readonly IVersionPlatformRepository $versionPlatformRepository;
    private readonly ILicenseRepository $licenceRepository;
    private readonly ICommentRepository $commentRepository;

    public function __construct(
            IResourceRepository $resourceRepository,
            IResourceStateRepository $resourceStateRepository,
            IResourceTypeRepository $resourceTypeRepository,
            IAreaRepository $areaRepository,
            IAuthorRepository $authorRepository,
            IResourceAuthorRepository $resourceAuthorRepository,
            IResourceFileRepository $resourceFileRepository,
            IUserRepository $userRepository,
            IVersionRepository $versionRepository,
            IVersionDeviceRepository $versionDeviceRepository,
            IVersionLangsRepository $versionLangsRepository,
            IVersionPlatformRepository $versionPlatformRepository,
            ILicenseRepository $licenceRepository,
            ICommentRepository $commentRepository,
        ) {
        $this->resourceRepository = $resourceRepository;
        $this->resourceStateRepository = $resourceStateRepository;
        $this->resourceTypeRepository = $resourceTypeRepository;
        $this->areaRepository = $areaRepository;
        $this->authorRepository = $authorRepository;
        $this->resourceAuthorRepository = $resourceAuthorRepository;
        $this->resourceFileRepository = $resourceFileRepository;
        $this->userRepository = $userRepository;
        $this->versionRepository = $versionRepository;
        $this->versionDeviceRepository = $versionDeviceRepository;
        $this->versionLangsRepository = $versionLangsRepository;
        $this->versionPlatformRepository = $versionPlatformRepository;
        $this->licenceRepository = $licenceRepository;
        $this->commentRepository = $commentRepository;
    }


    private function getFileUrl(string $path): string {

        return Storage::disk('s3')->url($path);

    }
    
    
    public function listResources(Filter $filter):Envelop{
        try{
            $res = new Envelop();
            
            $defaultFilter = new Filter();
            $defaultFilter->setPageSize(1000);
            
            $states = $this->resourceStateRepository->fetch($defaultFilter);

            
            $publishState = $states->firstOrFail(function (ResourceState $state){
                return $state->getCode() === 'PUBLISHED';
            });
            
            $filter->setStateId($publishState->getStateId());
            $count= $this->resourceRepository->countByFilter($filter);
            $resources = $this->resourceRepository->fetchByFilter($filter);
            
            $areas = $this->areaRepository->fetchByFilter($defaultFilter);
            $types = $this->resourceTypeRepository->fetchByFilter($defaultFilter);
            
            
            $resources->each(function (Resource $resource) use ($areas,$types, $states, $defaultFilter){
                $filterState = $states->firstOrFail(function (ResourceState $state) use ($resource){
                    return $state->getStateId() === $resource->getStateId();
                });
                $resource->setState($filterState);
                
                $resourceFiles = $this->resourceFileRepository->fetch($defaultFilter, $resource->getResourceId());
                $filterType = $types->firstOrFail(function (ResourceType $type) use ($resource){
                    return $type->getTypeId() === $resource->getTypeId();
                });
                $resource->setType($filterType);
                    
                if($resource->getAreaId() !== null){
                    $filterArea = $areas->first(function (Area $area) use ($resource){
                        return $area->getAreaId() === $resource->getAreaId();
                    });
                    
                    $resource->setArea($filterArea);
                    $resourceFiles->each(function (ResourceFile $file) use ($resource, $filterArea){
                        $path = "resources/{$filterArea->getCode()}/{$resource->getResourceId()}/files/{$file->getName()}";
                        $file->setUrl($this->getFileUrl($path));
                    });
                    $resource->setFiles($resourceFiles);
                }
                
                $filterComments = new Filter();
                $filterComments->setPageSize(10000);
                $filterComments->setStatus('PUBLISHED');
                $comments = $this->commentRepository->search($filterComments,$resource->getResourceId());
                $sum = $comments->sum(function (Comment $comment){
                    return $comment->getScore();
                });
                
                $resource->setRating($comments->count() > 0 ? $sum/$comments->count() : 0);                    
            });
            
            
            $res->setData($resources, $filter, $count,'resources');
            
            return $res;
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }
    
    public function findForView(string $resourceId):Resource {
        try{
            $filter = new Filter();
            $filter->setPageSize(100);
            $resource = $this->resourceRepository->findOrFail($resourceId);

            $type= $this->resourceTypeRepository->findById($resource->getTypeId());
            $state = $this->resourceStateRepository->findById($resource->getStateId());
            $area = $this->areaRepository->findById($resource->getAreaId());
            $authors = $this->resourceAuthorRepository->getAuthorsByResource($resourceId);
            $files =$this->resourceFileRepository->fetch($filter, $resource->getResourceId());

            $lastVersion = $this->versionRepository->findLastByResource($resourceId);
            $lastVersion->setDevices($this->versionDeviceRepository->fetchForVersion($lastVersion->getVersionId(),$filter));
            $lastVersion->setLanguages($this->versionLangsRepository->fetchByVersion($lastVersion->getVersionId(),$filter));
            $lastVersion->setPlatforms($this->versionPlatformRepository->fetchByVersion($lastVersion->getVersionId(),$filter));
            $lastVersion->setLicense($this->licenceRepository->find($lastVersion->getLicenseId()));
            $files->each(function (ResourceFile $file) use ($resource,$area){
                $path = "resources/{$area->getCode()}/{$resource->getResourceId()}/files/{$file->getName()}";
                $file->setUrl($this->getFileUrl($path));
            });
            
            $resource->setVersion($lastVersion);
            $resource->setAuthors($authors);
            $resource->setFiles($files);
            $resource->setArea($area);
            $resource->setType($type);
            $resource->setState($state);
            $resource->setUser($this->userRepository->findById($resource->getUserId()));

            return $resource;
        }
        catch (NotFoundException $ex){

            throw  new NotFoundException('La entidad buscada no existe.');
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }

    public function createResource(Resource $resource): Resource {
        try {
            $user = auth()->user();
            $exist = $this->resourceRepository->findByTitle($resource->getTitle());
            if($exist !== null){
                throw new BadRequestException('El titulo del recurso ya existe');
            }
            $area = $this->areaRepository->findById($resource->getAreaId());
            $type = $this->resourceTypeRepository->findById($resource->getTypeId());
            $state = $this->resourceStateRepository->findByCode('CREATED');

            $resource->setResourceId(Uuid::uuid4()->toString());
            $resource->setUserId($user->user_id);
            $resource->setDownloads(0);
            $resource->setStateId($state->getStateId());
            $resource->setAreaId($area->getAreaId());
            $resource->setTypeId($type->getTypeId());
            $resource->setCreatedAt(Carbon::now('utc'));
            $resource->setUpdatedAt(Carbon::now('utc'));

            $res = $this->resourceRepository->insert($resource);
            $res->setArea($area);
            $res->setType($type);
            $res->setState($state);
            $res->setUser($this->userRepository->findById($user->user_id));
            return $res;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (NotFoundException $ex){
            throw new BadRequestException('El area, tipo o estado del recurso no existen');
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error interno');
        }
    }

    public function fetchResources(Filter $filter):Envelop{
        try{
            $res = new Envelop();
            $count= $this->resourceRepository->countByFilter($filter);
            $resources = $this->resourceRepository->fetchByFilter($filter);
            $defaultFilter = new Filter();
            $defaultFilter->setPageSize(100);
            $areas = $this->areaRepository->fetchByFilter($defaultFilter);
            $types = $this->resourceTypeRepository->fetchByFilter($defaultFilter);
            $states = $this->resourceStateRepository->fetch($defaultFilter);

            $resources->each(function (Resource $resource) use ($areas,$types, $states){
                $filterState = $states->first(function (ResourceState $state) use ($resource){
                    return $state->getStateId() === $resource->getStateId();
                });
                $resource->setState($filterState);

                $filterType = $types->first(function (ResourceType $type) use ($resource){
                    return $type->getTypeId() === $resource->getTypeId();
                });
                $resource->setType($filterType);

                if($resource->getAreaId() !== null){
                    $filterArea = $areas->first(function (Area $area) use ($resource){
                       return $area->getAreaId() === $resource->getAreaId();
                    });
                $resource->setArea($filterArea);
                }
            });
            $res->setData($resources, $filter, $count,'resources');

            return $res;
        } catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function findResource(string $resourceId):Resource {
        try{
            $resource = $this->resourceRepository->findOrFail($resourceId);
            $type= $this->resourceTypeRepository->findById($resource->getTypeId());
            $state = $this->resourceStateRepository->findById($resource->getStateId());
            $area = null;
            if($resource->getAreaId() !== null){
                $area = $this->areaRepository->findById($resource->getAreaId());
            }

            $resource->setArea($area);
            $resource->setType($type);
            $resource->setState($state);
            $resource->setUser($this->userRepository->findById($resource->getUserId()));

            return $resource;
        }
        catch (NotFoundException $ex){
            throw  new NotFoundException('La entidad buscada no existe.');
        }
        catch (Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }

    public function update(Resource $resource):Resource{
        try {
            $exist = $this->resourceRepository->countForUpdate($resource);

            if($exist  !== 0){
                throw new BadRequestException('El titulo del recurso ya se encuentra utilizado con otro');
            }

            $area = $this->areaRepository->findById($resource->getAreaId());
            $type = $this->resourceTypeRepository->findById($resource->getTypeId());
            $state = $this->resourceStateRepository->findById($resource->getStateId());

            $resource->setStateId($state->getStateId());
            $resource->setAreaId($area->getAreaId());
            $resource->setTypeId($type->getTypeId());
            $resource->setUpdatedAt(Carbon::now('utc'));

            $result = $this->resourceRepository->update($resource);
            $result->setArea($area);
            $result->setType($type);
            $result->setState($state);
            $result->setUser($this->userRepository->findById($result->getUserId()));
            return $result;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (NotFoundException $ex){
            throw new BadRequestException('El area, tipo o estado del recurso no existen');
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error interno');
        }
    }

    public function updateState(Resource $resource){
        try {
            $current = $this->resourceRepository->findOrFail($resource->getResourceId());
            $targetState = $this->resourceStateRepository->findById($resource->getStateId());

            if($targetState->getCode() === 'PUBLISHED' || $targetState->getCode() === 'PENDING'){

                $filter = new Filter();
                $filter->setPageSize(1000);
                $version = $this->versionRepository->countByResource($resource->getResourceId(), $filter);
                $files = $this->resourceFileRepository->count($filter,$resource->getResourceId());
                $authors = $this->resourceAuthorRepository->countAuthorsByResource($resource->getResourceId());

                if($version === 0 || $files === 0 || $authors === 0){
                    throw new BadRequestException('No se puede publicar un recurso sin versiones, archivos o autores asociados');
                }
            }

            $current->setStateId($targetState->getStateId());
            $current->setUpdatedAt(Carbon::now('utc'));

            $res = $this->resourceRepository->updateState($current);
            return $this->findResource($res->getResourceId());
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error interno');
        }
    }

    public function addResourceAuthor(Author $author, string $resourceId): Author{
        try{
            $resource = $this->resourceRepository->findOrFail($resourceId);
            $exist = $this->authorRepository->findByEmail($author->getEmail());
            if($exist !== null){
                $count = $this->resourceAuthorRepository->countByAuthorAndResource($resourceId, $exist->getAuthorId());
                if($count > 0){
                    return $exist;
                }
                $this->resourceAuthorRepository->assignAuthorToResource($resourceId, $exist->getAuthorId());
                return $exist;
            }



            $author->setAuthorId(Uuid::uuid4()->toString());
            $author->setCreatedAt(Carbon::now('utc'));
            $author->setUpdatedAt(Carbon::now('utc'));
            $newAuthor = $this->authorRepository->insert($author);

            $this->resourceAuthorRepository->assignAuthorToResource($resource->getResourceId(), $newAuthor->getAuthorId());

            return $newAuthor;

        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function fetchAuthors(string $resourceId):Envelop{
        try{
            $envelop = new Envelop();
            $filter = new Filter();
            $filter->setPageSize(100);

            $count = $this->resourceAuthorRepository->countAuthorsByResource($resourceId);
            $authors = $this->resourceAuthorRepository->getAuthorsByResource($resourceId);
            $envelop->setData($authors, $filter, $count, 'authors');

            return $envelop;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function deleteAuthor(string $resourceId, string $authorId):Array{
        try{
            $this->resourceAuthorRepository->removeAuthorFromResource($resourceId, $authorId);
            return [
                'result'=>true,
                'message'=>'El autor ha sido removido del recurso'];
        }
        catch (Exception $ex) {
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function fetchFiles(string $resourceId):Envelop{
        try{
            $envelop = new Envelop();
            $filter = new Filter();
            $filter->setPageSize(100);

            $count = $this->resourceFileRepository->count($filter, $resourceId);
            $files = $this->resourceFileRepository->fetch($filter, $resourceId);
            $envelop->setData($files, $filter, $count, 'files');

            return $envelop;
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

    public function addFile(UploadedFile $uploadFile, string $resourceId, ResourceFile $resourceFile): ResourceFile{
        try{
            $resource = $this->resourceRepository->findOrFail($resourceId);
            $area = $this->areaRepository->findById($resource->getAreaId());
            $file = new ResourceFile();
            $file->setFileId(Uuid::uuid4()->toString());
            $file->setResourceId($resourceId);
            $file->setCreatedAt(Carbon::now('utc'));
            $file->setName($uploadFile->getClientOriginalName());
            $file->setExtension($uploadFile->getClientOriginalExtension());
            $file->setOption($resourceFile->getOption());
            $file->setType($resourceFile->getType());
            $exist = $this->resourceFileRepository->findByName($file->getName(), $resourceId);
            if($exist !== null){
                throw new BadRequestException('El nombre del archivo ya existe');
            }

            $dir = "resources/{$area->getCode()}/{$resourceId}/files";
            $name = $uploadFile->getClientOriginalName();

            $res = Storage::disk('s3')->putFileAs($dir, $uploadFile, $name);
            $file->setPath($res);
            $res = $this->resourceFileRepository->create($file);
            return $res;
        }
        catch (BadRequestException $ex){
            throw $ex;
        }
        catch (NotFoundException $ex){
            throw new BadRequestException('El recurso no existe');
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }

    }

    public function removeFile(string $resourceId, string $fileId): void{
        try{
            $file = $this->resourceFileRepository->find($fileId);
            $storageDisk = Storage::disk('s3');


            $storageDisk->delete($file->getPath());

            $this->resourceFileRepository->delete($fileId);
        }
        catch (NotFoundException $ex){
            throw new NotFoundException('El archivo no existe');
        }
        catch (Exception $ex) {
            dd($ex);
            throw new InternalErrorException('Ha ocurrido un error inesperado.');
        }
    }

}
