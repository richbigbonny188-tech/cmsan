<?php
/* --------------------------------------------------------------
  StyleEdit3Configuration.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities;

use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use JsonSerializable;
use stdClass;

/**
 * Class StyleEdit3Configuration
 */
class StyleEdit3Configuration implements StyleEdit3ConfigurationInterface, JsonSerializable
{
    /**
     * @var string
     */
    public const THEME_TYPE = 'Theme';
    
    /**
     * @var string
     */
    public const TEMPLATE_TYPE = 'Template';
    
    /**
     * @var string
     */
    protected $type;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var bool
     */
    protected $active;
    
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var array
     */
    protected $settings;
    
    /**
     * @var string
     */
    protected $style;
    
    /**
     * @var string
     */
    protected $customCss;

    /**
     * @var string
     */
    protected $version;
    
    
    /**
     * StyleEdit3Configuration constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $path
     * @param string $style
     * @param array  $settings
     * @param string $customCss
     * @param bool   $active
     * @param string $version
     */
    protected function __construct(
        string $name,
        string $type,
        string $path,
        string $style,
        array $settings,
        string $customCss,
        bool $active,
        string $version
    ) {
        $this->name      = $name;
        $this->type      = $type;
        $this->path      = $path;
        $this->settings  = $settings;
        $this->style     = $style;
        $this->customCss = $customCss;
        $this->active    = $active;
        $this->version   = $version;
    }
    
    
    /**
     * @param string $name
     * @param string $path
     * @param string $style
     * @param array  $settings
     * @param string $customCss
     * @param bool   $active
     * @param string $version
     *
     * @return StyleEdit3Configuration
     */
    public static function createForTheme(
        string $name,
        string $path,
        string $style,
        array $settings,
        string $customCss,
        bool $active,
        string $version
    ): self {
        $type = self::THEME_TYPE;
        
        return new static($name, $type, $path, $style, $settings, $customCss, $active, $version);
    }
    
    
    /**
     * @param string $name
     * @param string $path
     * @param string $style
     * @param array  $settings
     * @param string $customCss
     * @param bool   $active
     * @param string $version
     *
     * @return StyleEdit3Configuration
     */
    public static function createForTemplate(
        string $name,
        string $path,
        string $style,
        array $settings,
        string $customCss,
        bool $active,
        string $version
    ): self {
        $type = self::TEMPLATE_TYPE;
        
        return new static($name, $type, $path, $style, $settings, $customCss, $active, $version);
    }
    
    
    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
    
    
    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }
    
    
    /**
     * @return stdClass[]
     */
    public function settings(): array
    {
        return $this->settings;
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
        $result           = new stdClass;
        $result->name     = $this->name();
        $result->path     = $this->path();
        $result->type     = $this->type();
        $result->isActive = $this->isActive();
        $result->style    = $this->style();
        $result->version  = $this->version();

        return $result;
    }
    
    
    /**
     * @return string
     */
    public function style(): string
    {
        return $this->style;
    }
    
    
    /**
     * @return string
     */
    public function customCss(): string
    {
        return $this->customCss;
    }


    /**
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }
}