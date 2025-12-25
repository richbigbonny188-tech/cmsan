<?php
/* --------------------------------------------------------------
 CachedMenuFactory.php 2020-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Factories;

use Gambio\Admin\Layout\Menu\Factories\Helper\Verifier;
use Gambio\Admin\Layout\Menu\Models\Cached\Collections\MenuItems;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuGroup;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuItem;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuSettings;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuUrl;
use Webmozart\Assert\Assert;

/**
 * Class CachedMenuFactory
 * @package Gambio\Admin\Layout\Menu\Factories
 */
class PostCacheMenuFactory
{
    /**
     * @var MenuSettings
     */
    private $settings;
    
    /**
     * @var Verifier
     */
    private $verifier;
    
    
    /**
     * CachedMenuFactory constructor.
     *
     * @param MenuSettings $settings
     * @param Verifier     $verifier
     */
    public function __construct(
        MenuSettings $settings,
        Verifier $verifier
    ) {
        $this->settings = $settings;
        $this->verifier = $verifier;
    }
    
    
    /**
     * Creates menu items from the given data.
     *
     * @param array       $data
     *
     * @param string|null $connectedPage
     *
     * @return MenuItems
     */
    public function createMenuItems(array $data, string $connectedPage = null): MenuItems
    {
        $items            = new MenuItems();
        $requestUri       = MenuUrl::fromUri($this->settings->requestUri());
        $connectedPageUrl = $connectedPage ? MenuUrl::fromUri("{$this->settings->adminUrl()}/$connectedPage") : null;
        
        foreach ($data as $dataset) {
            $url = $this->buildUrl($dataset);
            if ($this->verifier->isAllowed($url)) {
                $menuUrl  = MenuUrl::fromUri($url);
                $isActive = $menuUrl->equals($requestUri)
                            || (null !== $connectedPageUrl && $menuUrl->equals($connectedPageUrl));
                
                $menuItem = new MenuItem($dataset['title'], $url, $isActive);
                $items->add($menuItem);
            }
        }
        
        return $items;
    }
    
    
    /**
     * Creates a menu group from the given dataset.
     *
     * @param array     $dataset
     * @param MenuItems $items
     *
     * @return MenuGroup
     */
    public function createMenuGroup(array $dataset, MenuItems $items): MenuGroup
    {
        Assert::keyExists($dataset, 'id');
        Assert::keyExists($dataset, 'title');
        Assert::keyExists($dataset, 'class');
        
        return new MenuGroup(
            $dataset['id'],
            $dataset['title'],
            $dataset['class'],
            $dataset['brand'] ?? null,
            $dataset['type'] ?? null,
            $items
        );
    }
    
    
    /**
     * Builds the menu items url.
     *
     * @param array $menuItem
     *
     * @return string
     */
    private function buildUrl(array $menuItem): string
    {
        $url = "{$this->settings->adminUrl()}/{$menuItem['link']}";
        if (array_key_exists('linkParam', $menuItem)) {
            $url .= '?' . $menuItem['linkParam'];
        }
        
        return $url;
    }
}