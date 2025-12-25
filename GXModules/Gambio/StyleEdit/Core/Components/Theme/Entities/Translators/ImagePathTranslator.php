<?php
/*--------------------------------------------------------------------------------------------------
    ImagePathTranslator.php 2020-03-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\Components\Theme\Entities\Translators;

use Gambio\StyleEdit\Configurations\ShopBaseUrl;

/**
 * Class ImagePathTranslator
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities\Translators
 */
class ImagePathTranslator
{
    protected $baseUrl;
    
    
    /**
     * ImagePathTranslator constructor.
     *
     * @param ShopBaseUrl $baseUrl
     */
    public function __construct(ShopBaseUrl $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * @param \stdClass $themeConfig
     */
    public function translateContent(\stdClass $themeConfig): void
    {
        $themeConfig->thumbnail = $this->absolutePathForThumbnail($themeConfig);
    }
    
    
    /**
     * @param $themeConfig
     *
     * @return string
     */
    protected function absolutePathForThumbnail($themeConfig): string
    {
        $shop_root = dirname(__DIR__, 8);
        $webPath   = $this->baseUrl->value() . 'themes/' . $themeConfig->id . '/' . $themeConfig->thumbnail;
        $filePath  = $shop_root . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $themeConfig->id
                     . DIRECTORY_SEPARATOR . $themeConfig->thumbnail;
	
	    return file_exists($filePath) && is_file($filePath)
	           && $themeConfig->thumbnail !== '' ? $webPath : $this->fallbackImage();
    }
    
    
    /**
     * @return string
     */
    protected function fallbackImage(): string
    {
        return 'PREVIEW_NOT_AVAILABLE';
    }
}