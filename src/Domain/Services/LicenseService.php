<?php
namespace UesPlay\Domain\Services;

use Exception;
use Carbon\Carbon;
use UesPlay\Domain\Exceptions\BadRequestException;
use UesPlay\Domain\Exceptions\InternalErrorException;
use UesPlay\Domain\Exceptions\NotFoundException;
use Ramsey\Uuid\Uuid;

use UesPlay\Domain\Interfaces\ILicenseRepository;
use UesPlay\Domain\Entities\License;
use UesPlay\Domain\Helpers\Filter;
use UesPlay\Domain\Helpers\Envelop;


class LicenseService
{
    private readonly ILicenseRepository $licenseRepository;

    public function __construct(ILicenseRepository $licenseRepository) {
        $this->licenseRepository = $licenseRepository;
    }
    
    public function search(Filter $filter):Envelop{
        try{
            $envelop = new Envelop();
            
            $data = $this->licenseRepository->search($filter);
            $count = $this->licenseRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'licenses');
            
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }        
    }
    
    public function fetch(Filter $filter):Envelop{
        try{
            $envelop = new Envelop();
            $filter->setEnabled(true);
            $data = $this->licenseRepository->search($filter);
            $count = $this->licenseRepository->count($filter);
            $envelop->setData($data, $filter, $count, 'licences');
            
            return $envelop;
        } catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function create(License $license):License{
        try{
            $exists = $this->licenseRepository->findByName($license->getName(), null);
            if($exists != null){
                throw new BadRequestException("Ya existe una licencia con el nombre ".$license->getName());
            }
            
            $license->setLicenseId(Uuid::uuid4()->toString());
            $license->setCreatedAt(Carbon::now('utc'));
            $license->setUpdatedAt(Carbon::now('utc'));

            $res = $this->licenseRepository->insert($license);
            return $res;        
        }
        catch (BadRequestException $bre){
            throw $bre;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function update(License $license):License{
        try{
            $exists = $this->licenseRepository->findByName($license->getName(), $license->getLicenseId());
            
            if($exists != null){
                throw new BadRequestException("Ya existe otra licencia con el nombre ".$license->getName());
            }
            
            $current = $this->licenseRepository->find($license->getLicenseId());
            $current -> setName($license->getName());
            $current->setVersion($license->getVersion());
            $current->setDescription($license->getDescription());
            $current->setEnabled($license->isEnabled());
            $current->setUpdatedAt(Carbon::now('utc'));
            
            $res = $this->licenseRepository->update($license);
            return $res;
        }
        catch (BadRequestException $bre){
            throw $bre;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function trash(string $licenseId):Array{
        try{            
            $current = $this->licenseRepository->find($licenseId);
            $res = $this->licenseRepository->delete($current->getLicenseId());
            return ['result'=>$res]; 
        }
        catch (NotFoundException $bre){
            throw $bre;
        }
        catch (Exception $ex) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
    
    public function summaryLicenses():array{
        try{
            $summary = $this->licenseRepository->getSummary();
            return [
                'entity' => 'Licencias',
                'total' => $summary['total'],
                'active' => $summary['active']
            ];
        } catch (Exception) {
            throw new InternalErrorException("Ha ocurrido un error inesperado");
        }
    }
}

