<?php
/* --------------------------------------------------------------
  ExportContentZoneJsonService.php 2019-12-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services;

use ContentIdentification;
use ContentNotFoundException;
use ContentReadServiceInterface;
use ContentText;
use Exception;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\Factories\StyleEditInfoElementFactory;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Services\Entities\StyleEditInfoElementContent;
use InfoElementContentToThemeJsonConverterInterface;
use stdClass;

/**
 * Class ExportContentZoneJsonService
 * @package Gambio\StyleEdit\Core\Services
 */
class ExportContentZoneJsonService
{
    /**
     * @var string[]
     */
    protected $contentManagerWidgetIds = ['text', 'code'];
    
    /**
     * @var array
     */
    protected $defaultContentManagerEntries = [
        1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 14,
        30, 61, 62, 63, 64, 65, 66, 67, 68,
        69, 82, 198, 199, 3210123, 3300001, 3889891,
        3889895, 3889896, 3889897, 3889898, 3889899,
        4311000, 4311001, 4311002, 4311003, 4311004,
        4311005, 4311006, 4321001, 4321002, 4321003,
        4321004, 4321005, 4321006, 4321007
    ];
    
    /**
     * @var string[]
     */
    protected $contentManagerImages = [];
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    /**
     * @var ContentReadServiceInterface
     */
    protected $reader;
    
    /**
     * @var InfoElementContentToThemeJsonConverterInterface
     */
    protected $converter;
    
    /**
     * @var stdClass[]
     */
    protected $entries = [];
    
    /**
     * @var int[]
     */
    protected $ids;
    
    /**
     * @var StyleEditInfoElementFactory
     */
    protected $factory;
    
    
    public function __construct()
    {
        $this->filesystem = SingletonPrototype::instance()->get('FilesystemAdapterShopRoot');
        $this->reader     = SingletonPrototype::instance()->get(ContentReadServiceInterface::class);
        $this->converter  = SingletonPrototype::instance()->get(InfoElementContentToThemeJsonConverterInterface::class);
        $this->factory    = SingletonPrototype::instance()->get(StyleEditInfoElementFactory::class);
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function addContentsToThemeJson(string $themeId): void
    {
        $this->ids = $this->requiredContentGroupIds($themeId);
        
        if (count($this->ids)) {
            
            foreach ($this->ids as $id) {
                try {
                    $contentIdentification = SingletonPrototype::instance()->get(ContentIdentification::class, $id);
                    $element = $this->reader->findById($contentIdentification);
                } catch (ContentNotFoundException $notFoundException) {
                    continue;
                }
    
                $newContent = $this->changePathOfContentManagerEntryImages($element);
                $element    = $newContent ?? $element;
                $element    = $this->factory->createWithAlias($element, $themeId);
                
                $this->entries[] = $this->converter->convert($element);
            }
            
            if (count($this->entries)) {
                
                $this->updateThemeJson($themeId);
            }
        }
    }
    
    
    /**
     * @param \InfoElementContent $infoElement
     *
     * @return StyleEditInfoElementContent|null
     * @throws Exception
     */
    protected function changePathOfContentManagerEntryImages(\InfoElementContent $infoElement): ?StyleEditInfoElementContent
    {
        $localImagePattern = '#src=("|\')([^"\']+)("|\')#';
        $newContent        = [];
        
        foreach ($infoElement->texts() as $text) {
            
            /** @var ContentText $text */
            $content = $text->content();
            if (preg_match_all($localImagePattern, $content, $matches)) {
    
                $images = array_unique($matches[2]);
                $images = array_filter($images, static function(string $image) : bool
                {
                    return preg_match('#^(http|//)#',$image) === 0;
                });
                
                foreach ($images as $image) {
    
                    $this->contentManagerImages[] = $image;
    
                    $pattern = '#' . preg_quote($image, '#') . '#';
                    $content = preg_replace($pattern, $this->imagePathInTheme($image), $content);
                    
                    $newContent[$text->languageCode()] = $content;
                }
            }
        }
        
        return count($newContent) ? $this->factory->createWithNewContent($infoElement, $newContent) : null;
    }
    
    
    /**
     * @param string $imagePath
     *
     * @return string
     */
    protected function imagePathInTheme(string $imagePath): string
    {
        return 'public/theme/images/' . basename($imagePath);
    }
    
    /**
     * @param string $themeId
     *
     * @return int[]
     * @throws FileNotFoundException
     */
    protected function requiredContentGroupIds(string $themeId): array
    {
        $result = [];
        $jsons  = $this->getContentZoneJsonPaths($themeId);
        
        if (count($jsons)) {
            
            foreach ($jsons as $json) {
                
                $jsonStr    = $this->filesystem->read($json);
                $jsonObject = json_decode($jsonStr, false);
                
                foreach ($jsonObject->rows as &$row) {
                    
                    foreach ($row->cols as &$col) {
                        
                        if (count($col->widgets) !== 0) {
                            
                            foreach ($col->widgets as &$widget) {
                                
                                if (in_array($widget->id, $this->contentManagerWidgetIds, true)) {
                                    
                                    $contentGroup = $this->getContentGroupFromWidget($widget);
                                    
                                    if (!in_array($contentGroup, $this->defaultContentManagerEntries, true)
                                        && $contentGroup !== 0) {
                                        
                                        $result[] = $contentGroup;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param stdClass $widget
     *
     * @return int
     */
    protected function getContentGroupFromWidget(stdClass $widget): int
    {
        return $widget->fieldsets[0]->options[0]->contentGroup ?? 0;
    }
    
    
    /**
     * @param string $themeId
     *
     * @return string[]
     */
    protected function getContentZoneJsonPaths(string $themeId): array
    {
        $result          = [];
        $contentZonePath = 'themes' . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR . 'contentzones';
        
        $jsonFiles = $this->filesystem->listContents($contentZonePath);
        
        if (count($jsonFiles)) {
            
            foreach ($jsonFiles as $file) {
                
                if ($file['extension'] === 'json') {
                    
                    $result[] = $file['path'];
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     */
    protected function updateThemeJson(string $themeId): void
    {
        $themeJsonPath = 'themes' . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR . 'theme.json';
        $themeJsonStr  = $this->filesystem->read($themeJsonPath);
        $themeJson     = json_decode($themeJsonStr, false);
        
        if (!isset($themeJson->contents)) {
            
            $themeJson->contents = new stdClass;
        }
        
        if (!isset($themeJson->contents->infoElements)) {
            
            $themeJson->contents->infoElements = [];
        }
        
        if (count($themeJson->contents->infoElements)) {
            
            $ids                               = $this->ids;
            $themeJson->contents->infoElements = array_filter($themeJson->contents->infoElements,
                static function (stdClass $element) use ($ids) {
                    return !in_array($element->id, $ids);
                });
        }
        
        foreach ($this->entries as $entry) {
            
            $themeJson->contents->infoElements[] = $entry;
        }
        
        $this->filesystem->update($themeJsonPath, json_encode($themeJson, JSON_PRETTY_PRINT));
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     */
    public function resetProductWidgetsProductSelection(string $themeId): void
    {
        $jsons = $this->getContentZoneJsonPaths($themeId);
    
        if (count($jsons)) {
    
            foreach ($jsons as $json) {
        
                $jsonStr    = $this->filesystem->read($json);
                $jsonObject = json_decode($jsonStr, false);
        
                foreach ($jsonObject->rows as &$row) {
            
                    foreach ($row->cols as &$col) {
                
                        if (count($col->widgets) !== 0) {
                    
                            foreach ($col->widgets as &$widget) {
    
                                if ($widget->id === 'product') {
                                    
                                    $this->resetProductWidget($widget);
                                }
                                
                                if ($widget->id === 'productlist') {
                                    
                                    $this->resetProductListWidget($widget);
                                }
                            }
                        }
                    }
                }
                
                $this->updateContentZoneJson($json, $jsonObject);
            }
        }
    }
    
    
    /**
     * @param stdClass $productWidget
     */
    protected function resetProductWidget(stdClass $productWidget): void
    {
        if ($productWidget->fieldsets[1]->options[0]->value instanceof stdClass) {
    
            $productWidget->fieldsets[1]->options[0]->value = '';
        }
        
        $productWidget->fieldsets[1]->options[0]->endpoint = '../../../../admin/admin.php?do=StyleEditProductSearch/byTerm';
    }
    
    
    /**
     * @param stdClass $productListWidget
     */
    protected function resetProductListWidget(stdClass $productListWidget): void
    {
        $listConfiguration = $productListWidget->fieldsets[2]->options[0];
        $listItems         = $listConfiguration->items;
        $listItems->products = $listItems->products ?? new stdClass();
        $listItems->category = $listItems->category ?? new stdClass();
        
        if ($listItems->listType->value === 'category') {
            //  default is "own-list"
            $listItems->listType->value = $listItems->listType->default;
        }
        
        if (count($listConfiguration->items->products->value)) {
    
            $listConfiguration->items->products->value = [];
        }
        
        $listItems->products->endpoint = '../../../../admin/admin.php?do=StyleEditProductSearch/byTerm';
        $listItems->category->endpoint = '../../../../admin/admin.php?do=StyleEditCategorySearch/byTerm';
    }
    
    
    /**
     * @param string   $json
     * @param stdClass $jsonObject
     *
     * @throws FileNotFoundException
     */
    protected function updateContentZoneJson(string $json, stdClass $jsonObject): void
    {
        $this->filesystem->update($json, json_encode($jsonObject, JSON_PRETTY_PRINT));
    }
    
    
    /**
     * @return string[]
     */
    public function contentManagerImages(): array
    {
        return $this->contentManagerImages;
    }
}