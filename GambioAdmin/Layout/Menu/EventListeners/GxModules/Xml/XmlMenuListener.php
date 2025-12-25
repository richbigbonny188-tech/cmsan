<?php
/* --------------------------------------------------------------
   XmlMenuListener.php 2021-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml;

use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Factories\CacheMenuFactory;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\TemplateEngine\Engines\Smarty\SmartyEngine;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;
use Throwable;
use function Gambio\Core\Logging\logger;

/**
 * Class XmlMenuListener
 * @package    Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml
 * @deprecated Support for XML menu files will ends in upcoming feature version. Use JSON menu files instead.
 * @codeCoverageIgnore
 */
class XmlMenuListener
{
    /**
     * @var XmlMenuFinder
     */
    private $fileFinder;
    
    /**
     * @var CacheMenuFactory
     */
    private $menuFactory;
    
    /**
     * @var SmartyEngine
     */
    private $smartyEngine;
    
    /**
     * @var Environment
     */
    private $environment;
    
    
    /**
     * XmlMenuListener constructor.
     *
     * @param XmlMenuFinder    $fileFinder
     * @param CacheMenuFactory $menuFactory
     * @param SmartyEngine     $smartyEngine
     * @param Environment      $environment
     */
    public function __construct(
        XmlMenuFinder $fileFinder,
        CacheMenuFactory $menuFactory,
        SmartyEngine $smartyEngine,
        Environment $environment
    ) {
        $this->fileFinder   = $fileFinder;
        $this->menuFactory  = $menuFactory;
        $this->smartyEngine = $smartyEngine;
        $this->environment  = $environment;
    }
    
    
    /**
     * Events callback function.
     *
     * @param CoreMenuDataCollected $event
     *
     * @return CoreMenuDataCollected
     */
    public function __invoke(CoreMenuDataCollected $event): CoreMenuDataCollected
    {
        $menuFiles = $this->fileFinder->findMenuFiles();
        
        foreach ($menuFiles as $menuFile) {
            try {
                $this->addFile($event, $menuFile);
            } catch (Throwable $e) {
                if ($this->environment->isDev()) {
                    logger('legacy_admin_menu')->warning($e->getMessage(), ['file' => $menuFile]);
                }
            }
        }
        
        return $event;
    }
    
    
    /**
     * Processes a menu file and adds them to the menu cache.
     *
     * @param CoreMenuDataCollected $event
     * @param string                $menuFile
     *
     * @throws RenderingFailedException
     */
    private function addFile(CoreMenuDataCollected $event, string $menuFile): void
    {
        $contents = $this->smartyEngine->render($menuFile);
        
        $xml   = simplexml_load_string($contents);
        $json  = json_encode($xml);
        $array = json_decode($json, true);
        
        $groupData = $array['menugroup'];
        
        if ($this->isGroupDataSet($groupData)) {
            $this->addGroup($event, $groupData);
            
            return;
        }
        
        if (is_array($groupData)) {
            foreach ($groupData as $menuGroup) {
                if ($this->isGroupDataSet($menuGroup)) {
                    $this->addGroup($event, $menuGroup);
                }
            }
        }
    }
    
    
    /**
     * Adds a menu group.
     *
     * @param CoreMenuDataCollected $event
     * @param array                 $groupDataSet
     */
    private function addGroup(CoreMenuDataCollected $event, array $groupDataSet): void
    {
        $groupAttributes = $groupDataSet['@attributes'];
        $this->fixTitle($groupAttributes);
        
        $group    = $this->menuFactory->createMenuGroup($groupAttributes, null);
        $itemData = $groupDataSet['menuitem'];
        
        if ($this->hasAttributes($itemData)) {
            $attributes = $itemData['@attributes'];
            $this->fixTitle($attributes);
            
            $item = $this->menuFactory->createMenuItem($attributes, null);
            $group->add($item);
            $event->addGroup($group);
            
            return;
        }
        if (is_array($itemData)) {
            foreach ($itemData as $menuItem) {
                $attributes = $menuItem['@attributes'];
                $this->fixTitle($attributes);
                
                $item = $this->menuFactory->createMenuItem($attributes, null);
                $group->add($item);
            }
        }
        
        $event->addGroup($group);
    }
    
    
    /**
     * Fixes the "title" element in $dataset.
     *
     * This is important due to compatibility reasons. The "txt." prefix will be replaced with "admin_menu.",
     * so the text manager can translate the title later.
     *
     * @param array $dataset
     */
    private function fixTitle(array &$dataset): void
    {
        if (!array_key_exists('title', $dataset)) {
            $dataset['title'] = '';
            
            return;
        }
        
        $regex = '/\{\$(.*)\}/';
        preg_match($regex, $dataset['title'], $matches);
        
        if (!array_key_exists(1, $matches)) {
            return;
        }
        $match    = $matches[1];
        $segments = explode('|', $match);
        $match    = $segments[0];
        
        $dataset['title'] = str_replace('txt.', 'admin_menu.', $match);
    }
    
    
    /**
     * Returns true if $dataset represents a menu group.
     *
     * @param array $dataset
     *
     * @return bool
     */
    private function isGroupDataSet(array $dataset): bool
    {
        return $this->hasAttributes($dataset) && array_key_exists('menuitem', $dataset);
    }
    
    
    /**
     * Returns true if data set contains key "@attributes".
     *
     * @param array $dataset
     *
     * @return bool
     */
    private function hasAttributes(array $dataset): bool
    {
        return array_key_exists('@attributes', $dataset);
    }
}
