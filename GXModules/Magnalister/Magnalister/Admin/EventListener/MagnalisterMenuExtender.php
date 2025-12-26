<?php
/* --------------------------------------------------------------
 MagnalisterMenuExtender.php 2021-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Magnalister\Magnalister\Admin\EventListener;

use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Factories\CacheMenuFactory;
use Gambio\Admin\Layout\Menu\Filter\Condition;
use Gambio\Admin\Layout\Menu\Filter\Conditions;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuGroup;

/**
 * Class MagnalisterMenuExtender
 *
 * @package GXModules\Magnalister\Magnalister\Admin\EventListener
 */
class MagnalisterMenuExtender
{
    /**
     * @var CacheMenuFactory
     */
    private $menuFactory;
    
    
    /**
     * MagnalisterMenuExtender constructor.
     *
     * @param CacheMenuFactory $menuFactory
     */
    public function __construct(CacheMenuFactory $menuFactory)
    {
        $this->menuFactory = $menuFactory;
    }
    
    
    /**
     * @param CoreMenuDataCollected $event
     *
     * @return CoreMenuDataCollected
     */
    public function __invoke(CoreMenuDataCollected $event): CoreMenuDataCollected
    {
        $menuGroup = $this->createMenuGroup();
        
        if (function_exists('magnaGenerateNavStructure')) {
            $menuItems = $this->getMenuStructure();
            foreach ($menuItems as $index => $menuItem) {
                $menuItem['sort'] = $index + 1;
                $menuGroup->add($this->menuFactory->createMenuItem($menuItem));
            }
        } else {
            $itemData = [
                'title' => 'module_center_module.box_magnalister_config',
                'link'  => 'admin.php?do=EmbeddedModule/magnalister',
                'sort'  => 1
            ];
            $menuGroup->add($this->menuFactory->createMenuItem($itemData));
        }
        
        $event->addGroup($menuGroup);
        
        return $event;
    }
    
    
    /**
     * @return array
     */
    private function getMenuStructure(): array
    {
        $menuStructure = magnaGenerateNavStructure();
        
        foreach ($menuStructure as $key => &$menuItem) {
            if (array_key_exists('url', $menuItem)) {
                $menuItem['url'] = str_replace('magnalister.php?',
                                               'admin.php?do=EmbeddedModule/magnalister&',
                                               $menuItem['url']);
            }
        }
        unset($menuItem);
        
        $menuItems = [
            [
                'title' => 'module_center_module.box_magnalister_start',
                'link'  => 'admin.php?do=EmbeddedModule/magnalister'
            ]
        ];
        
        $maxTitleLength = 25;
        foreach ($menuStructure as $menuItem) {
            $title = !empty($menuItem['label']) ? "{$menuItem['title']} {$menuItem['label']}" : $menuItem['title'];
            $title = substr($title, 0, $maxTitleLength);
            
            $menuItems[$menuItem['key']] = [
                'title' => $title,
                'link'  => $menuItem['url'],
            ];
        }
        
        return array_values($menuItems);
    }
    
    
    /**
     * @return MenuGroup
     */
    private function createMenuGroup(): MenuGroup
    {
        $conditions = new Conditions(Condition::create('configActive', ['MODULE_CENTER_MAGNALISTER_INSTALLED']));
        $groupData  = [
            'id'    => 'BOX_HEADING_MAGNALISTER',
            'title' => 'module_center_module.box_magnalister_heading',
            'class' => 'fa fa-tachometer',
            'sort'  => 65
        ];
        
        return $this->menuFactory->createMenuGroup($groupData, $conditions);
    }
}