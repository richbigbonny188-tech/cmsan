<?php
/*--------------------------------------------------------------
   Image.php 2021-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\Entities;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageAltTitles;
use Gambio\Admin\Modules\ImageList\Model\Collections\ImageTitles;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImageUrl;
use Webmozart\Assert\Assert;

/**
 * Class Image
 * @package Gambio\Admin\Modules\ImageList\Model\Entities
 */
class Image
{
    /**
     * @var ImagePath
     */
    private $imagePath;
    
    /**
     * @var ImageUrl
     */
    private $webPath;
    
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
     * Image constructor.
     *
     * @param ImagePath      $imagePath
     * @param ImageUrl       $webPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     */
    private function __construct(
        ImagePath $imagePath,
        ImageUrl $webPath,
        ImageTitles $titles,
        ImageAltTitles $altTitles,
        int $sortOrder
    ) {
        $this->imagePath = $imagePath;
        $this->webPath   = $webPath;
        $this->titles    = $titles;
        $this->altTitles = $altTitles;
        $this->sortOrder = $sortOrder;
    }
    
    
    /**
     * @param ImagePath      $imagePath
     * @param ImageUrl       $webPath
     * @param ImageTitles    $titles
     * @param ImageAltTitles $altTitles
     * @param int            $sortOrder
     *
     * @return Image
     */
    public static function create(
        ImagePath $imagePath,
        ImageUrl $webPath,
        ImageTitles $titles,
        ImageAltTitles $altTitles,
        int $sortOrder
    ): Image {
        
        Assert::greaterThanEq($sortOrder, 0, 'Expected sort order greater than or equal to %2$s. Got: %s');
        
        return new self($imagePath, $webPath, $titles, $altTitles, $sortOrder);
    }
    
    
    /**
     * @return string
     */
    public function relativePath(): string
    {
        return $this->imagePath->relativePath();
    }
    
    
    /**
     * @return string
     */
    public function absolutePath(): string
    {
        return $this->imagePath->absolutePath();
    }
    
    
    /**
     * @return string
     */
    public function webPath(): string
    {
        return $this->webPath->value();
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
    
    
    /**
     * @param ImageTitles $titles
     *
     * @return Image
     */
    public function withTitles(ImageTitles $titles): Image
    {
        return new self($this->imagePath, $this->webPath, $titles, $this->altTitles, $this->sortOrder);
    }
    
    
    /**
     * @param ImageAltTitles $altTitles
     *
     * @return Image
     */
    public function withAltTitles(ImageAltTitles $altTitles): Image
    {
        return new self($this->imagePath, $this->webPath, $this->titles, $altTitles, $this->sortOrder);
    }
    
    
    /**
     * @param int $sortOder
     *
     * @return Image
     */
    public function withSortOrder(int $sortOder): Image
    {
        return new self($this->imagePath, $this->webPath, $this->titles, $this->altTitles, $sortOder);
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'relativePath' => $this->relativePath(),
            'url'          => $this->webPath(),
            'titles'       => $this->titles()->toArray(),
            'altTitles'    => $this->altTitles()->toArray(),
            'sortOrder'    => $this->sortOrder(),
        ];
    }
}