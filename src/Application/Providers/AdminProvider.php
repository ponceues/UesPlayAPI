<?php
namespace UesPlay\Application\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use UesPlay\Domain\Services\RolService;
use UesPlay\Domain\Services\UserService;
use UesPlay\Domain\Services\EmailService;

use UesPlay\Domain\Interfaces\IRolRepository;
use UesPlay\Domain\Interfaces\IMenuRepository;
use UesPlay\Domain\Interfaces\IPermissionRepository;
use UesPlay\Domain\Interfaces\IUserRepository;
use UesPlay\Domain\Interfaces\IUserStateRepository;
use UesPlay\Domain\Interfaces\IEmailTemplateRepository;
use UesPlay\Domain\Interfaces\ISendEmailFunction;
use UesPlay\Domain\Interfaces\IAreaRepository;
use UesPlay\Domain\Interfaces\IPlatformRepository;
use UesPlay\Domain\Interfaces\IDeviceRepository;
use UesPlay\Domain\Interfaces\IUserAreasRepository;
use UesPlay\Domain\Interfaces\IResourceRepository;
use UesPlay\Domain\Interfaces\IResourceStateRepository;
use UesPlay\Domain\Interfaces\IResourceTypeRepository;
use UesPlay\Domain\Interfaces\IAuthorRepository;
use UesPlay\Domain\Interfaces\IResourceAuthorRepository;
use UesPlay\Domain\Interfaces\IResourceFileRepository;
use UesPlay\Domain\Interfaces\ILanguageRepository;
use UesPlay\Domain\Interfaces\IVersionRepository;
use UesPlay\Domain\Interfaces\IVersionDeviceRepository;
use UesPlay\Domain\Interfaces\IVersionPlatformRepository;
use UesPlay\Domain\Interfaces\IVersionLangsRepository;
use UesPlay\Domain\Interfaces\ILicenseRepository;
use UesPlay\Domain\Interfaces\IMediaTypeRepository;
use UesPlay\Domain\Interfaces\IMediaGenreRepository;
use UesPlay\Domain\Interfaces\ICommentRepository;

use UesPlay\Infrastructure\Repositories\RolRepository;
use UesPlay\Infrastructure\Repositories\MenuRepository;
use UesPlay\Infrastructure\Repositories\PermissionRepository;
use UesPlay\Infrastructure\Repositories\UserRepository;
use UesPlay\Infrastructure\Repositories\UserStateRepository;
use UesPlay\Infrastructure\Repositories\EmailTemplateRepository;
use UesPlay\Infrastructure\Functions\SendEmailFunction;
use UesPlay\Infrastructure\Repositories\AreaRepository;
use UesPlay\Infrastructure\Repositories\ResourceTypeRepository;
use UesPlay\Infrastructure\Repositories\PlatformRepository;
use UesPlay\Infrastructure\Repositories\DeviceRepository;
use UesPlay\Infrastructure\Repositories\UserAreasRepository;
use UesPlay\Infrastructure\Repositories\ResourceRepository;
use UesPlay\Infrastructure\Repositories\ResourceStateRepository;
use UesPlay\Infrastructure\Repositories\AuthorRepository;
use UesPlay\Infrastructure\Repositories\ResourceAuthorRepository;
use UesPlay\Infrastructure\Repositories\ResourceFileRepository;
use UesPlay\Infrastructure\Repositories\LanguageRepository;
use UesPlay\Infrastructure\Repositories\VersionRepository;
use UesPlay\Infrastructure\Repositories\VersionDeviceRepository;
use UesPlay\Infrastructure\Repositories\VersionPlatformRepository;
use UesPlay\Infrastructure\Repositories\VersionLangsRepository;
use UesPlay\Infrastructure\Repositories\LicenseRepository;
use UesPlay\Infrastructure\Repositories\MediaTypeRepository;
use UesPlay\Infrastructure\Repositories\MediaGenreRepository;
use UesPlay\Infrastructure\Repositories\CommentRepository;

class AdminProvider extends ServiceProvider {

    public function register(): void{
        //Interfaces . . . 
        $this->app->bind(IRolRepository::class,RolRepository::class);
        $this->app->bind(IPermissionRepository::class, PermissionRepository::class);
        $this->app->bind(IMenuRepository::class, MenuRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IUserStateRepository::class, UserStateRepository::class);
        $this->app->bind(IEmailTemplateRepository::class, EmailTemplateRepository::class);
        $this->app->bind(ISendEmailFunction::class, SendEmailFunction::class);           
        $this->app->bind(IAreaRepository::class, AreaRepository::class);
        $this->app->bind(IPlatformRepository::class, PlatformRepository::class);
        $this->app->bind(IDeviceRepository::class, DeviceRepository::class);
        $this->app->bind(IUserAreasRepository::class, UserAreasRepository::class);
        $this->app->bind(IResourceRepository::class, ResourceRepository::class);
        $this->app->bind(IResourceStateRepository::class, ResourceStateRepository::class);
        $this->app->bind(IResourceTypeRepository::class, ResourceTypeRepository::class);
        $this->app->bind(IAuthorRepository::class, AuthorRepository::class);
        $this->app->bind(IResourceAuthorRepository::class, ResourceAuthorRepository::class);
        $this->app->bind(IResourceFileRepository::class, ResourceFileRepository::class);
        $this->app->bind(ILanguageRepository::class, LanguageRepository::class);
        $this->app->bind(IVersionRepository::class, VersionRepository::class);
        $this->app->bind(IVersionDeviceRepository::class, VersionDeviceRepository::class);
        $this->app->bind(IVersionPlatformRepository::class, VersionPlatformRepository::class);
        $this->app->bind(IVersionLangsRepository::class, VersionLangsRepository::class);
        $this->app->bind(ILicenseRepository::class, LicenseRepository::class);
        $this->app->bind(IMediaTypeRepository::class, MediaTypeRepository::class);
        $this->app->bind(IMediaGenreRepository::class, MediaGenreRepository::class);
        $this->app->bind(ICommentRepository::class, CommentRepository::class);
        //services . . .
        
        $this->app->scoped(RolService::class,function(Application $app){
            return new RolService(
                $app->make(IRolRepository::class),
                $app->make(IMenuRepository::class),
                $app->make(IPermissionRepository::class) 
            );
        });
        
        $this->app->scoped(UserService::class,function(Application $app){
            return new UserService(
                $app->make(IUserRepository::class),
                $app->make(IUserStateRepository::class),
                $app->make(IAreaRepository::class),
                $app->make(IUserAreasRepository::class),
                $app->make(IRolRepository::class),
                $app->make(EmailService::class)
            );
        });
        
        $this->app->scoped(EmailService::class,function(Application $app){
            return new EmailService(
                $app->make(IEmailTemplateRepository::class),
                $app->make(ISendEmailFunction::class),
            );
        });
    }
    
    public function boot():void{

    }
}
