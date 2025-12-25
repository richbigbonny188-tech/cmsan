<?php
/* --------------------------------------------------------------
  ContentZoneContent.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities;

use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\ContentGeneratorInterface;
use Gambio\StyleEdit\Core\Components\ContentZone\Interfaces\UpdatableContentZoneContentInterface;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\PersistableContentInterface;
use InvalidArgumentException;
use KeyValueCollection;
use stdClass;

/**
 * Class ContentZoneContent
 */
class ContentZoneContent extends KeyValueCollection
    implements ContentGeneratorInterface, PersistableContentInterface, UpdatableContentZoneContentInterface, AlwaysNewStrategyInterface
{
    /**
     * @var string
     */
    protected const STYLE_TAG_PATTERN = '/\s+<style>([^<]+)<\/style>/m';
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var ?stdClass
     */
    protected $jsonObject;
    
    
    /**
     * Class Constructor
     *
     * @param array         $keyValueArray
     * @param string        $id
     * @param stdClass|null $jsonObject
     */
    public function __construct(array $keyValueArray, string $id, ?stdClass $jsonObject=null)
    {
        parent::__construct($keyValueArray);
        
        $this->id         = $id;
        $this->jsonObject = $jsonObject;
    }
    
    
    /**
     * @param stdClass $jsonObject
     *
     * @return ContentZoneContent
     * @throws Exception
     */
    public static function createFromJsonObject(stdClass $jsonObject): self
    {
        if (!isset($jsonObject->id, $jsonObject->rows) || !is_array($jsonObject->rows)) {
            
            throw new InvalidArgumentException;
        }
        
        $id   = $jsonObject->id;
        $rows = [];
        
        if (count($jsonObject->rows) > 0) {
            
            foreach ($jsonObject->rows as $rowIndex => $row) {
                
                if (!isset($row->id)) {
                    
                    $row->id = $jsonObject->id . '-row-' . ($rowIndex + 1);
                }
                
                $rows[] = ContentZoneRow::createFromJsonObject($row);
            }
        }
        
        return SingletonPrototype::instance()->get(static::class, $rows, $id, $jsonObject);
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $html = '';
        
        if (count($this->getArray()) > 0) {
            /** @var ContentZoneRow $row */
            foreach ($this->getArray() as $row) {
                
                $rowHtml = $row->htmlContent($currentLanguage);
                
                $html .= PHP_EOL . $rowHtml;
            }
        }
        
        return $this->mergeStyleTags($html);
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result       = $this->jsonObject;
        $result->type = 'content-zone';
        $result->id   = $this->id;
        
        $rows = [];
        
        if (count($this->getArray())) {
            /** @var ContentZoneRow $row */
            foreach ($this->getArray() as $row) {
                
                $rows[] = $row->jsonSerialize();
            }
        }
    
        $result->rows        = $rows;
        $result->shopVersion = $this->getCurrentShopVersion();
        
        return $result;
    }
    
    
    /**
     * @param string $contentZoneHtml
     *
     * @return string
     */
    private function mergeStyleTags(string $contentZoneHtml): string
    {
        if (preg_match_all(self::STYLE_TAG_PATTERN, $contentZoneHtml, $matches)) {
            
            $contentZoneStyle = '<style>' . '</style>';
            
            // adding the style to the single style-tag
            foreach ($matches[1] as $style) {
                
                $contentZoneStyle = str_replace('</style>', $style . '</style>', $contentZoneStyle);
            }
            
            // removing the original style tags from the html
            foreach ($matches[0] as $styleTag) {
                
                $contentZoneHtml = str_replace($styleTag, '', $contentZoneHtml);
            }
            
            $contentZoneHtml = $contentZoneStyle . PHP_EOL . $contentZoneHtml;
        }
        
        return $contentZoneHtml;
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     * @throws Exception
     */
    public function previewContent(?Language $currentLanguage): string
    {
        return $this->htmlContent($currentLanguage);
    }
    
    
    public function persist(): void
    {
        foreach ($this->getIterator() as $row) {
            
            $row->persist();
        }
    }
    
    
    /**
     * @return string
     */
    protected function getCurrentShopVersion(): string
    {
        $shopRoot    = dirname(__DIR__, 7);
        $releaseInfo = $shopRoot . '/release_info.php';
        
        if (file_exists($releaseInfo)) {
            
            include $releaseInfo;
            
            /** @var string $gx_version */
            return $gx_version;
        }
        
        return '';
    }
    
    public function update(): void
    {
        foreach ($this->getIterator() as $row) {
            /** @var ContentZoneRow $row */
            $row->update();
        }
    }
}