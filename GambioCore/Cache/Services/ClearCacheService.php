<?php
/* --------------------------------------------------------------
   ClearCacheService.php 2021-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\Services;

/**
 * Interface ClearCacheService
 *
 * @package Gambio\Core\Cache\Services
 */
interface ClearCacheService
{
    /**
     * Clears all module based caches.
     */
    public function clearModuleCaches(): void;
    
    
    /**
     * Clears all product based caches.
     */
    public function clearProductCaches(): void;
    
    
    /**
     * Clears all product properties based caches.
     */
    public function clearProductPropertiesCaches(): void;
    
    
    /**
     * Clears all system based caches.
     */
    public function clearSystemCaches(): void;
    
    
    /**
     * Clears all template based caches.
     */
    public function clearTemplateCaches(): void;
    
    
    /**
     * Clears all text phrases based caches.
     */
    public function clearTextCaches(): void;
    
    
    /**
     * Adds another namespace to the module based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToModuleCaches(string $namespace): void;
    
    
    /**
     * Adds another namespace to the product based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToProductCaches(string $namespace): void;
    
    
    /**
     * Adds another namespace to the product properties based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToProductPropertiesCaches(string $namespace): void;
    
    
    /**
     * Adds another namespace to the system based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToSystemCaches(string $namespace): void;
    
    
    /**
     * Adds another namespace to the template based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToTemplateCaches(string $namespace): void;
    
    
    /**
     * Adds another namespace to the text phrases based caches.
     *
     * @param string $namespace
     */
    public function addNamespaceToTextCaches(string $namespace): void;
}