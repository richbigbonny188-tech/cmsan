<?php
/*--------------------------------------------------------------------------------------------------
    ClearCacheCommand.php 2019-10-22
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Options\Commands;

use Gambio\StyleEdit\Adapters\Interfaces\CacheCleanerInterface;
use Gambio\StyleEdit\Core\Command\CommandInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;

/**
 * Class ClearCacheCommand
 * @package Gambio\StyleEdit\Core\Options\Commands
 */
class ClearCacheCommand implements CommandInterface
{
    
    /**
     * @var CacheCleanerInterface
     */
    protected $cacheCleaner;

    /**
     * @var CurrentThemeInterface
     */
    protected $currentTheme;

    /**
     * @var bool
     */
    protected $clearShopCache;


    /**
     * ClearCacheCommand constructor.
     *
     * @param CacheCleanerInterface $cacheCleaner
     * @param CurrentThemeInterface $currentTheme
     */
    public function __construct(CacheCleanerInterface $cacheCleaner, CurrentThemeInterface $currentTheme)
    {
        $this->cacheCleaner = $cacheCleaner;
        $this->currentTheme = $currentTheme;
    }
    
    
    /**
     * Execute the command
     */
    public function execute(): void
    {
        $this->cacheCleaner->clearThemeCache($this->currentTheme->id());

        if ($this->clearShopCache) {
            $this->cacheCleaner->clearShopCache();
        }
    }
    
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
    
    
    /**
     * @param \Gambio\StyleEdit\Core\Options\Entities\OptionInterface $option
     */
    public function setOption(OptionInterface $option): void
    {
        // TODO: Implement setOption() method.
    }

    /**
     * @param bool $clearShopCache
     */
    public function setClearShopCache(bool $clearShopCache): void
    {
        $this->clearShopCache = $clearShopCache;
    }
}