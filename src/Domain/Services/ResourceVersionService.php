<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use UesPlay\Domain\Exceptions\NotFoundException;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Interfaces\IResourceRepository;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Interfaces\ILicenseRepository;
use UesPlay\Domain\Interfaces\IVersionRepository;
use UesPlay\Domain\Interfaces\IVersionDeviceRepository;
use UesPlay\Domain\Interfaces\IVersionLangsRepository;
use UesPlay\Domain\Interfaces\IVersionPlatformRepository;
use UesPlay\Domain\Helpers\Envelop;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Entities\Version;
use UesPlay\Domain\Entities\Platform;
use UesPlay\Domain\Entities\Device;
use UesPlay\Domain\Entities\Language;
use UesPlay\Domain\Interfaces\ILanguageRepository;
use UesPlay\Domain\Interfaces\IPlatformRepository;
use UesPlay\Domain\Interfaces\IDeviceRepository;
use UesPlay\Domain\Interfaces\IAreaRepository;

class ResourceVersionService
{
    private readonly IVersionRepository $versionRepository;
    private readonly IVersionDeviceRepository $versionDeviceRepository;
    private readonly IVersionLangsRepository $versionLangsRepository;
    private readonly IVersionPlatformRepository $versionPlatformRepository;
    private readonly IResourceRepository $resourceRepository;
    private readonly ILanguageRepository $languageRepository;
    private readonly IPlatformRepository $platformRepository;
    private readonly IDeviceRepository  $deviceRepository;
    private readonly IAreaRepository  $areaRepository;
    private readonly ILicenseRepository $licenseRepository;
    
    public function __construct(
        IVersionRepository $versionRepository,
        IVersionDeviceRepository $versionDeviceRepository,
        IVersionLangsRepository $versionLangsRepository,
        IVersionPlatformRepository $versionPlatformRepository,
        IResourceRepository $resourceRepository,
        ILanguageRepository $languageRepository,
        IPlatformRepository $platformRepository,
        IDeviceRepository  $deviceRepository,
        IAreaRepository  $areaRepository,
        ILicenseRepository $licenceRepository
    ) {
        $this->versionRepository = $versionRepository;
        $this->versionDeviceRepository = $versionDeviceRepository;
        $this->versionLangsRepository = $versionLangsRepository;
        $this->versionPlatformRepository = $versionPlatformRepository;
        $this->resourceRepository = $resourceRepository;
        $this->languageRepository = $languageRepository;
        $this->platformRepository = $platformRepository;
        $this->deviceRepository = $deviceRepository;
        $this->areaRepository = $areaRepository;
        $this->licenseRepository = $licenceRepository;
    }
    
    
    public function fetchByResource(string $resourceId, Filter $filter):Envelop
    {
        try{
            $envelop = new Envelop();
            $version = $this->versionRepository->fetchByResource($resourceId, $filter);
            $count = $this->versionRepository->countByResource($resourceId, $filter);
            
            $filterDf = new Filter();
            $filterDf->setPageSize(1000);
            $version->each(function(Version $version) use ($filterDf){
                $version->setDevices($this->versionDeviceRepository->fetchForVersion($version->getVersionId(), $filterDf));
                $version->setLanguages($this->versionLangsRepository->fetchByVersion($version->getVersionId(), $filterDf));
                $version->setPlatforms($this->versionPlatformRepository->fetchByVersion($version->getVersionId(), $filterDf));
                $version->setLicense($this->licenseRepository->find($version->getLicenseId()));
                
            });
            
            $envelop->setData($version, $filter, $count, 'versions');
            return $envelop;
            
        }catch(Exception $e){
            throw new InternalErrorException('Ha ocurrido un error interno.');
        }
    }
    
    public function createVersion(string $resourceId, Version $version, UploadedFile $file): Version
    {
        try{
            $resource = $this->resourceRepository->findOrFail($resourceId);
            $area = $this->areaRepository->findById($resource->getAreaId());
            
            $version->setVersionId(Uuid::uuid4()->toString());
            $fileName = $version->getVersionId().'.'.$file->getClientOriginalExtension();
            
            $version->setFileName($fileName);
            $version->setCreatedAt(Carbon::now('utc'));
            $result = $this->versionRepository->insert($version);
            
            $dir = "resources/{$area->getCode()}/{$resourceId}/source";

            Storage::disk('s3')->putFileAs($dir, $file, $fileName);
            
            $platformsCol = collect();
            $devicesCol = collect();
            $langsCol = collect();
            $filter = new Filter();
            $filter->setPageSize(1000);
            
            $langs = $this->languageRepository->fetch($filter);
            
            foreach($version->langs as $lang){
                $currentLang = $langs->first(function(Language $l) use ($lang) {
                    return $l->getLanguageId() === $lang->getLanguageId();
                });
                if(!$currentLang){
                    throw new BadRequestException('El idioma proporcionado no existe.');
                }
                $langsCol->push($currentLang);
                $this->versionLangsRepository->insert($version, $currentLang);
            }
            
            $platforms = $this->platformRepository->fetch($filter);
            foreach($version->getPlatforms() as $platform){
                $currentPlatform = $platforms->first(function(Platform $l) use ($platform) {
                    return $l->getPlatformId() === $platform->getPlatformId();
                });
                
                if(!$currentPlatform){
                    throw new BadRequestException('La plataforma proporcionada no existe.');
                }
                $platformsCol->push($currentPlatform);
                $this->versionPlatformRepository->insert($version->getVersionId(), $currentPlatform->getPlatformId());
            }
            
            $devices = $this->deviceRepository->fetchByFilter($filter);
            foreach($version->getDevices() as $device){
                $currentDevice = $devices->first(function(Device $l) use ($device) {
                    return $l->getDeviceId() === $device->getDeviceId();
                });
                
                if(!$currentDevice){
                    throw new BadRequestException('El dispositivo proporcionado no existe.');
                }
                $devicesCol->push($currentDevice);
                $this->versionDeviceRepository->insert($version, $currentDevice);
            }
            $result->setLanguages($langsCol);
            $result->setPlatforms($platformsCol);
            $result->setDevices($devicesCol);
            return $result;
        }catch(BadRequestException $e){
            throw $e;
        }
        catch(Exception $e){
            throw new InternalErrorException('Ha ocurrido un error interno.');
        }
    }

    
    public function dowloadVersion(string $resourceId, string $versionId, bool $isClient)
    {
        try {
            $resource = $this->resourceRepository->findOrFail($resourceId);
            
            $version = $this->versionRepository->find($versionId);
            if (!$version) {
                throw new NotFoundException('La versión no existe.');
            }
            $area = $this->areaRepository->findById($resource->getAreaId());
            $storagePath = "resources/{$area->getCode()}/{$resourceId}/source";
            $filePath = $storagePath . '/' . $version->getFileName();
            
            // Verificar si el archivo existe en S3
            if (!Storage::disk('s3')->exists($filePath)) {
                throw new NotFoundException('El archivo de la versión no existe.');
            }
            
            if($isClient){
                $resource->setDownloads($resource->getDownloads() + 1);
                $this->resourceRepository->update($resource);
            }

            // Descargar el archivo desde S3
            return Storage::disk('s3')->download($filePath, $version->getFileName());
        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalErrorException('Ha ocurrido un error interno.');
        }
    }
}