<?php
/* --------------------------------------------------------------
   ClearFileCacheService.php 2021-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\App;

use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\ClearCacheService;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ClearFileCacheService
 *
 * @package Gambio\Core\Cache\App
 */
class ClearFileCacheService implements ClearCacheService
{
    /**
     * @var CacheFactory
     */
    private $factory;
    
    /**
     * @var array<string, string>
     */
    private $namespaces;
    
    
    /**
     * ClearCacheService constructor.
     *
     * @param CacheFactory $factory
     */
    public function __construct(CacheFactory $factory)
    {
        $this->factory    = $factory;
        $this->namespaces = [
            'module'            => [],
            'product'           => [],
            'productProperties' => [],
            'system'            => [],
            'template'          => [],
            'text-phrases'      => [],
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearModuleCaches(): void
    {
        foreach ($this->namespaces['module'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearProductCaches(): void
    {
        foreach ($this->namespaces['product'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearProductPropertiesCaches(): void
    {
        foreach ($this->namespaces['productProperties'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearSystemCaches(): void
    {
        foreach ($this->namespaces['system'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearTemplateCaches(): void
    {
        foreach ($this->namespaces['template'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function clearTextCaches(): void
    {
        foreach ($this->namespaces['text-phrases'] as $namespace) {
            try {
                $this->factory->createPsrCacheFor($namespace)->clear();
            } catch (InvalidArgumentException $e) {
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addNamespaceToModuleCaches(string $namespace): void
    {
        $this->namespaces['module'][$namespace] = $namespace;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addNamespaceToProductCaches(string $namespace): void
    {
        $this->namespaces['product'][$namespace] = $namespace;
    }
    
    
    /**
     * Adds another namespace to the product properties based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToProductPropertiesCaches(string $namespace): void
    {
        $this->namespaces['productProperties'][$namespace] = $namespace;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addNamespaceToSystemCaches(string $namespace): void
    {
        $this->namespaces['system'][$namespace] = $namespace;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addNamespaceToTemplateCaches(string $namespace): void
    {
        $this->namespaces['template'][$namespace] = $namespace;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addNamespaceToTextCaches(string $namespace): void
    {
        $this->namespaces['text-phrases'][$namespace] = $namespace;
    }
}