<?php
/* --------------------------------------------------------------
   GXModulesCompontentsService.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App;

use Gambio\Core\GXModules\App\Data\ComponentsRegistryRepository;
use Gambio\Core\GXModules\Model\Collections\AdminMenuJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\AutoloaderRegistry;
use Gambio\Core\GXModules\Model\Collections\GX4ModuleRegistry;
use Gambio\Core\GXModules\Model\Collections\GXModuleJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\LanguageFileRegistry;
use Gambio\Core\GXModules\Model\Collections\RoutesRegistry;
use Gambio\Core\GXModules\Model\Collections\ServiceProviderRegistry;
use Gambio\Core\GXModules\Model\Collections\TemplateRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\AdminMenuJson;
use Gambio\Core\GXModules\Model\ValueObjects\Autoloader;
use Gambio\Core\GXModules\Model\ValueObjects\GX4Module;
use Gambio\Core\GXModules\Model\ValueObjects\GXModuleJson;
use Gambio\Core\GXModules\Model\ValueObjects\LanguageFile;
use Gambio\Core\GXModules\Model\ValueObjects\Routes;
use Gambio\Core\GXModules\Model\ValueObjects\ServiceProvider;
use Gambio\Core\GXModules\Model\ValueObjects\Template;
use Gambio\Core\GXModules\Services\GXModulesComponentsService as GXModulesComponentsServiceInterface;

/**
 * Class GXModulesComponentsService
 *
 * @package Gambio\Core\GXModules\App
 */
class GXModulesComponentsService implements GXModulesComponentsServiceInterface
{
    /**
     * @var ComponentsRegistryRepository
     */
    private $repository;
    
    
    /**
     * GXModulesComponentsService constructor.
     *
     * @param ComponentsRegistryRepository $repository
     */
    public function __construct(ComponentsRegistryRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGXModuleJsons(): GXModuleJsonRegistry
    {
        /** @var GXModuleJsonRegistry $registry */
        $registry = $this->repository->getRegistry(GXModuleJson::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGX4Modules(): GX4ModuleRegistry
    {
        /** @var GX4ModuleRegistry $registry */
        $registry = $this->repository->getRegistry(GX4Module::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getServiceProviders(): ServiceProviderRegistry
    {
        /** @var ServiceProviderRegistry $registry */
        $registry = $this->repository->getRegistry(ServiceProvider::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAutoloaderFiles(): AutoloaderRegistry
    {
        /** @var AutoloaderRegistry $registry */
        $registry = $this->repository->getRegistry(Autoloader::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTemplates(): TemplateRegistry
    {
        /** @var TemplateRegistry $registry */
        $registry = $this->repository->getRegistry(Template::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getRoutes(): RoutesRegistry
    {
        /** @var RoutesRegistry $registry */
        $registry = $this->repository->getRegistry(Routes::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdminMenuJsons(): AdminMenuJsonRegistry
    {
        /** @var AdminMenuJsonRegistry $registry */
        $registry = $this->repository->getRegistry(AdminMenuJson::type());
        
        return $registry;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLanguageFiles(): LanguageFileRegistry
    {
        /** @var LanguageFileRegistry $registry */
        $registry = $this->repository->getRegistry(LanguageFile::type());
        
        return $registry;
    }
}