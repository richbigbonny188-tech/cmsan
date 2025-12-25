<?php
/*--------------------------------------------------------------
   PostInstallationCacheFileCreator.php 2023-05-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

use Gambio\Core\Cache\App\SafeFileCache;
use Gambio\Core\Cache\Services\CacheFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PostInstallationCacheFileCreator
 */
class PostInstallationCacheFileCreator extends PostInstallationCacheFileCreator_parent
{
    protected const CACHE_NAMESPACE = 'post-installation';
    protected const CACHE_TTL       = 10;
    
    
    public function proceed()
    {
        parent::proceed();
        
        try {
            $this->cache()->set('executed', true, static::CACHE_TTL);
        } catch (ContainerExceptionInterface|NotFoundExceptionInterface) {
        }
    }
    
    
    /**
     * @return SafeFileCache
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function cache(): SafeFileCache
    {
        return LegacyDependencyContainer::getInstance()
            ->get(CacheFactory::class)
            ->createCacheFor(static::CACHE_NAMESPACE);
    }
}