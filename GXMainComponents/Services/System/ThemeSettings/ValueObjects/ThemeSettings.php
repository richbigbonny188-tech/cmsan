<?php
/* --------------------------------------------------------------
  ThemeSettings.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\ValueObjects;

use Gambio\GX\Services\System\ThemeSettings\ValueObjects\Interfaces\ThemeSettingsInterface;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\ThemeIdNotSuppliedException;
use JsonSerializable;
use stdClass;

/**
 * Class ThemeSettings
 */
class ThemeSettings implements ThemeSettingsInterface, JsonSerializable
{
    /**
     * @var stdClass
     */
    protected $themeJson;
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var bool
     */
    protected $active;
    
    /**
     * @var string;
     */
    protected $path;
    
    
    /**
     * ThemeSettings constructor.
     *
     * @param stdClass $themeJson
     * @param string   $path
     */
    protected function __construct(stdClass $themeJson, string $path)
    {
        if (!isset($themeJson->id)) {
            throw new ThemeIdNotSuppliedException(['path' => $path]);
        }
        
        $this->id        = $themeJson->id;
        $this->themeJson = $themeJson;
        $this->active    = isset($themeJson->active) && $themeJson->active === true;
        $this->path      = $path;
    }
    
    
    /**
     * id matches directory name
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
    
    
    /**
     * @param bool $isActive
     */
    public function setActive(bool $isActive): void
    {
        $this->active = $isActive;
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
        $result = clone $this->themeJson;
        
        if ($this->isActive()) {
            
            $result->active = true;
        } else {
            
            unset($result->active);
        }
        
        return $result;
    }
    
    
    /**
     * @param stdClass $json
     * @param string   $path
     *
     * @return ThemeSettingsInterface
     */
    public static function createFromJsonObject(stdClass $json, string $path): ThemeSettingsInterface
    {
        return new static($json, $path);
    }
    
    
    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }
}