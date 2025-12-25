<?php
/*--------------------------------------------------------------------------------------------------
    ResolverVariantSettings.php 2019-10-16
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Json;

/**
 * Class ResolverVariantSettings
 * @package Gambio\StyleEdit\Core\Components\Variant\Json
 */
class ResolverVariantSettings
{
    protected $basePath;
    protected $parentId;
    protected $parentTheme;
    protected $theme;
    protected $variant;
    protected $variantOption;
    
    
    /**
     * ResolverVariantSettings constructor.
     *
     * @param $variantOption
     * @param $variant
     * @param $theme
     * @param $basePath
     * @param $parentTheme
     * @param $parentId
     */
    protected function __construct($variantOption, $variant, $theme, $basePath, $parentTheme, $parentId)
    {
        $this->variantOption = $variantOption;
        $this->variant       = $variant;
        $this->theme         = $theme;
        $this->basePath      = $basePath;
        $this->parentTheme   = $parentTheme;
        $this->parentId      = $parentId;
    }
    
    
    /**
     * @param $settings
     * @param $path
     *
     * @return ResolverVariantSettings
     */
    public static function createFromSettingsAndPath($settings, $path): self
    {
        $path           = explode(DIRECTORY_SEPARATOR, dirname($path));
        $variantOption  = array_pop($path);
        $variant        = array_pop($path);
        $variantsFolder = array_pop($path);
        $theme          = array_pop($path);
        $themeBasePath  = implode(DIRECTORY_SEPARATOR, $path);
        $parentTheme    = is_object($settings) ? ($settings->theme ?? 'SELF') : $settings;
        $parentId       = is_object($settings) ? ($settings->id ?? 'SELF') : 'SELF';
        
        if ($parentId == 'SELF') {
            $parentId = implode(DIRECTORY_SEPARATOR, [$variant, $variantOption]);
        }
        
        if(count(explode(DIRECTORY_SEPARATOR, $parentId)) === 1){
            $parentId = $variant.DIRECTORY_SEPARATOR.$parentId;
        }
        
        return new static($variantOption, $variant, $theme, $themeBasePath, $parentTheme, $parentId);
    }
    
    
    /**
     * @return mixed
     */
    public function basePath()
    {
        return $this->basePath;
    }
    
    
    /**
     * @return mixed
     */
    public function theme()
    {
        return $this->theme;
    }
    
    
    /**
     * @return mixed
     */
    public function variant()
    {
        return $this->variant;
    }
    
    
    /**
     * @return mixed
     */
    public function variantOption()
    {
        return $this->variantOption;
    }
    
    
    /**
     * @return mixed
     */
    public function parentTheme()
    {
        return $this->parentTheme;
    }
    
    
    /**
     * @return mixed
     */
    public function parentId()
    {
        return $this->parentId;
    }
    
}