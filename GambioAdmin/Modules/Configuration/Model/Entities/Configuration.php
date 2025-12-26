<?php
/* --------------------------------------------------------------
   Configuration.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Entities;

use Gambio\Admin\Modules\Configuration\Model\Collections\Tags;
use Gambio\Admin\Modules\Configuration\Model\ValueObjects\LanguageConfigurationValues;
use JsonSerializable;

/**
 * Class Configuration
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Entities
 */
class Configuration implements JsonSerializable
{
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var bool|string|LanguageConfigurationValues
     */
    private $value;
    
    /**
     * @var string
     */
    private $label;
    
    /**
     * @var string
     */
    private $tooltip;
    
    /**
     * @var Type
     */
    private $type;
    
    /**
     * @var Tags
     */
    private $tags;
    
    
    /**
     * Configuration constructor.
     *
     * @param string      $key
     * @param bool|string $value
     * @param string      $label
     * @param string      $tooltip
     * @param Type        $type
     * @param Tags        $tags
     */
    private function __construct(
        string $key,
        $value,
        string $label,
        string $tooltip,
        Type $type,
        Tags $tags
    ) {
        $this->key     = $key;
        $this->value   = $value;
        $this->label   = $label;
        $this->tooltip = $tooltip;
        $this->type    = $type;
        $this->tags    = $tags;
    }
    
    
    /**
     * @param string $key
     * @param bool   $value
     * @param string $label
     * @param string $tooltip
     * @param Type   $type
     * @param Tags   $tags
     *
     * @return Configuration
     */
    public static function createWithBoolValue(
        string $key,
        bool $value,
        string $label,
        string $tooltip,
        Type $type,
        Tags $tags
    ): Configuration {
        return new self($key, $value, $label, $tooltip, $type, $tags);
    }
    
    
    /**
     * @param string $key
     * @param string $value
     * @param string $label
     * @param string $tooltip
     * @param Type   $type
     * @param Tags   $tags
     *
     * @return Configuration
     */
    public static function createWithStringValue(
        string $key,
        string $value,
        string $label,
        string $tooltip,
        Type $type,
        Tags $tags
    ): Configuration {
        return new self($key, $value, $label, $tooltip, $type, $tags);
    }
    
    
    /**
     * @param string                      $key
     * @param LanguageConfigurationValues $value
     * @param string                      $label
     * @param string                      $tooltip
     * @param Type                        $type
     * @param Tags                        $tags
     *
     * @return Configuration
     */
    public static function createWithLanguageDependentValues(
        string $key,
        LanguageConfigurationValues $value,
        string $label,
        string $tooltip,
        Type $type,
        Tags $tags
    ): Configuration {
        return new self($key, $value, $label, $tooltip, $type, $tags);
    }
    
    
    /**
     * @return string[]
     */
    public function tagIds(): array
    {
        $tagIds = [];
        foreach ($this->tags as $tag) {
            $tagIds[] = $tag->id();
        }
        
        return array_unique($tagIds);
    }
    
    
    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'key'     => $this->key,
            'value'   => $this->value,
            'label'   => $this->label,
            'tooltip' => $this->tooltip,
            'type'    => $this->type,
            'tags'    => $this->tagIds(),
        ];
    }
}