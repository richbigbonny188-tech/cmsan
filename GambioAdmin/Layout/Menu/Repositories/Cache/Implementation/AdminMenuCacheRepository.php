<?php
/* --------------------------------------------------------------
 AdminMenuCacheRepository.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation;

use Exception;
use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;
use Gambio\Admin\Layout\Menu\Repositories\Cache\AdminMenuCacheRepository as AdminMenuCacheRepositoryInterface;
use Gambio\Admin\Modules\Language\Model\Language;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdminMenuCacheRepository
 * @package Gambio\Admin\Layout\Menu\Repositories\Cache
 */
class AdminMenuCacheRepository implements AdminMenuCacheRepositoryInterface
{
    /**
     * @var MenuCache
     */
    private $cache;
    
    /**
     * @var MenuMapper
     */
    private $mapper;
    
    /**
     * @var FilterFactory
     */
    private $filterFactory;
    
    /**
     * @var LanguageService
     */
    private $languageService;
    
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    
    /**
     * AdminMenuCacheRepository constructor.
     *
     * @param MenuCache                $cache
     * @param MenuMapper               $mapper
     * @param FilterFactory            $filterFactory
     * @param LanguageService          $languageService
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        MenuCache $cache,
        MenuMapper $mapper,
        FilterFactory $filterFactory,
        LanguageService $languageService,
        EventDispatcherInterface $dispatcher
    ) {
        $this->cache           = $cache;
        $this->mapper          = $mapper;
        $this->filterFactory   = $filterFactory;
        $this->languageService = $languageService;
        $this->dispatcher      = $dispatcher;
    }
    
    
    /**
     * @inheritDoc
     */
    public function hasCache(): bool
    {
        return $this->cache->hasCache();
    }
    
    
    /**
     * @inheritDoc
     */
    public function buildCache(MenuGroups $groups, array $menuData): void
    {
        $groups->reset();
        
        $this->mapper->map($groups, $menuData);
        try {
            $this->dispatcher->dispatch(new CoreMenuDataCollected($groups));
        } catch (Exception $e) {
        }
        
        $groups->filter($this->filterFactory);
        $groups->sort();
        
        $this->cache->buildMenuCache($groups);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCache(): array
    {
        return $this->cache->getCache();
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCache(): void
    {
        $languages = $this->languageService->getAvailableAdminLanguages();
        
        /** @var Language $language */
        foreach ($languages as $language) {
            $this->cache->deleteCache($language->id());
        }
    }
}