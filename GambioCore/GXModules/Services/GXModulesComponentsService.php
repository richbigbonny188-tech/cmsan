<?php
/* --------------------------------------------------------------
   GXModulesComponentsService.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Services;

use Gambio\Core\GXModules\Model\Collections\AdminMenuJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\AutoloaderRegistry;
use Gambio\Core\GXModules\Model\Collections\GX4ModuleRegistry;
use Gambio\Core\GXModules\Model\Collections\GXModuleJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\LanguageFileRegistry;
use Gambio\Core\GXModules\Model\Collections\RoutesRegistry;
use Gambio\Core\GXModules\Model\Collections\ServiceProviderRegistry;
use Gambio\Core\GXModules\Model\Collections\TemplateRegistry;

/**
 * Interface GXModulesComponentsService
 *
 * @package Gambio\Core\GXModules\Services
 */
interface GXModulesComponentsService
{
    /**
     * Returns a collection of all available GXModule JSON files.
     *
     * @return GXModuleJsonRegistry
     */
    public function getGXModuleJsons(): GXModuleJsonRegistry;
    
    
    /**
     * Returns a collection of all available GXModule module classes.
     *
     * @return GX4ModuleRegistry
     */
    public function getGX4Modules(): GX4ModuleRegistry;
    
    
    /**
     * Returns a collection of all available GXModule service providers.
     *
     * @return ServiceProviderRegistry
     */
    public function getServiceProviders(): ServiceProviderRegistry;
    
    
    /**
     * Returns a collection of all available GXModule autoloaders.
     *
     * @return AutoloaderRegistry
     */
    public function getAutoloaderFiles(): AutoloaderRegistry;
    
    
    /**
     * Returns a collection of all available GXModule templates.
     *
     * @return TemplateRegistry
     */
    public function getTemplates(): TemplateRegistry;
    
    
    /**
     * Returns a collection of all available GXModule routing files.
     *
     * @return RoutesRegistry
     */
    public function getRoutes(): RoutesRegistry;
    
    
    /**
     * Returns a collection of all available GXModule menu JSON files.
     *
     * @return AdminMenuJsonRegistry
     */
    public function getAdminMenuJsons(): AdminMenuJsonRegistry;
    
    
    /**
     * Returns a collection of all available GXModule language files.
     *
     * @return LanguageFileRegistry
     */
    public function getLanguageFiles(): LanguageFileRegistry;
}