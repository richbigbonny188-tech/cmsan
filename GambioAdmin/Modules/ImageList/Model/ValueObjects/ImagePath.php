<?php
/*--------------------------------------------------------------
   ImagePath.php 2021-11-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

/**
 * Class ImagePath
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImagePath
{
    /**
     * @var string
     */
    private $absolutePath;
    
    /**
     * @var string
     */
    private $relativePath;
    
    
    /**
     * ImagePath constructor.
     *
     * @param string $absolutePath
     * @param string $relativePath
     */
    private function __construct(string $absolutePath, string $relativePath)
    {
        $this->absolutePath = $absolutePath;
        $this->relativePath = $relativePath;
    }
    
    
    /**
     * @param string $relativePath
     *
     * @return ImagePath
     */
    public static function create(string $relativePath): ImagePath
    {
        $relativePath = ltrim($relativePath, '/');
        $absolutePath = dirname(__DIR__, 5) . '/images/product_images/original_images/' . $relativePath;
        
        return new self($absolutePath, $relativePath);
    }
    
    
    /**
     * @return string
     */
    public function relativePath(): string
    {
        return $this->relativePath;
    }
    
    
    /**
     * @return string
     */
    public function absolutePath(): string
    {
        return $this->absolutePath;
    }
}