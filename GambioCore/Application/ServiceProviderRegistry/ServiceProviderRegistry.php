<?php
/* --------------------------------------------------------------
 ServiceProviderRegistry.php 2022-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ServiceProviderRegistry;

use Gambio\Core\Application\ValueObjects\Environment;

/**
 * Class ServiceProviderRegistry
 *
 * @package Gambio\Core\Application\ServiceProviderRegistry
 * @deprecated In favor of `Gambio\Core\Application\ModuleRegistry\ServiceProviderLoader`
 */
class ServiceProviderRegistry
{
    /**
     * @var ServiceProviderRegistryCache
     */
    private $cache;
    
    /**
     * @var Environment
     */
    private $environment;
    
    
    /**
     * ServiceProviderRegistry constructor.
     *
     * @param ServiceProviderRegistryCache $cache
     * @param Environment                  $environment
     */
    public function __construct(ServiceProviderRegistryCache $cache, Environment $environment)
    {
        $this->cache       = $cache;
        $this->environment = $environment;
    }
    
    
    /**
     * Returns the service provider registry.
     *
     * @param ServiceProviderRegistryType $type
     *
     * @return array
     */
    public function getRegistry(ServiceProviderRegistryType $type): array
    {
        if (!$this->environment->isDev() && $this->cache->hasRegistry($type)) {
            return $this->cache->getRegistry($type);
        }
        
        $registry = $this->createRegistry($type);
        $this->cache->setRegistry($type, $registry);
        
        return $registry;
    }
    
    
    /**
     * @param ServiceProviderRegistryType $type
     *
     * @return array
     */
    private function createRegistry(ServiceProviderRegistryType $type): array
    {
        $modulesPath     = $type->modulesPath();
        $namespacePrefix = $type->namespacePrefix();
        $registry        = [];
        
        foreach (glob($type->pattern()) as $serviceProviderPath) {
            $moduleNamespace = str_replace([$modulesPath, '.php', '/'], ['', '', '\\'], $serviceProviderPath);
            $registry[]      = $namespacePrefix . $moduleNamespace;
        }
        
        return $registry;
    }
}