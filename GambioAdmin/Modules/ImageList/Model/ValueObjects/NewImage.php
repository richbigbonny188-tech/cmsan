<?php
/*--------------------------------------------------------------
   NewImage.php 2021-06-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageAltTitles;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageTitles;

/**
 * Class NewImage
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class NewImage
{
    /**
     * @var ImagePath
     */
    private $localPath;
    
    /**
     * @var ImageTitles
     */
    private $titles;
    
    /**
     * @var ImageAltTitles
     */
    private $altTitles;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    
    /**
     * NewImage constructor.
     *
     * @param ImagePath      $localPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     */
    private function __construct(
        ImagePath $localPath,
        ImageTitles $titles,
        ImageAltTitles $altTitles,
        int $sortOrder
    ) {
        $this->localPath = $localPath;
        $this->titles    = $titles;
        $this->altTitles = $altTitles;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @param ImagePath      $localPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     *
     * @return NewImage
     */
    public static function create(
        ImagePath $localPath,
        ImageTitles $titles,
        ImageAltTitles $altTitles,
        int $sortOrder
    ): NewImage {
        
        return new self($localPath, $titles, $altTitles, $sortOrder);
    }
    
    
    /**
     * @return string
     */
    public function relativePath(): string
    {
        return $this->localPath->relativePath();
    }
    
    
    /**
     * @return string
     */
    public function absolutePath(): string
    {
        return $this->localPath->absolutePath();
    }
    
    
    /**
     * @return ImageTitles
     */
    public function titles(): ImageTitles
    {
        return $this->titles;
    }
    
    
    /**
     * @return ImageAltTitles
     */
    public function altTitles(): ImageAltTitles
    {
        return $this->altTitles;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}