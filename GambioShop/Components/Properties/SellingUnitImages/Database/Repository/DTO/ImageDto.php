<?php
/*--------------------------------------------------------------------
 ImageDto.php 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Repository\DTO;

/**
 * Class ImageDto
 * @package Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Dtos
 */
class ImageDto
{
    /**
     * @var string
     */
    protected $relativePath;
    /**
     * @var string
     */
    protected $altText;
    
    /**
     * @var int
     */
    protected $imageNumber;
    
    /**
     * @var string
     */
    protected $infoPath;
    
    /**
     * @var string
     */
    protected $thumbnailPath;
    
    /**
     * @var string
     */
    protected $popupPath;

    /**
     * @var string
     */
    protected $galleryPath;


    /**
     * ImageDto constructor.
     *
     * @param string $relativePath
     * @param string $altText
     * @param int $imageNumber
     * @param string $infoPath
     * @param string $thumbnailPath
     * @param string $popupPath
     * @param string $galleryPath
     */
    public function __construct(
        string $relativePath,
        string $altText,
        int $imageNumber,
        string $infoPath,
        string $thumbnailPath,
        string $popupPath,
        string $galleryPath
    ) {
        $this->relativePath  = $relativePath;
        $this->altText       = $altText;
        $this->imageNumber   = $imageNumber;
        $this->infoPath      = $infoPath;
        $this->thumbnailPath = $thumbnailPath;
        $this->popupPath     = $popupPath;
        $this->galleryPath    = $galleryPath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function relativePath(): string
    {
        return $this->relativePath;
    }
    
    
    /**
     * @return string
     */
    public function altText(): string
    {
        return $this->altText;
    }
    
    
    /**
     * @return int
     */
    public function imageNumber(): int
    {
        return $this->imageNumber;
    }
    
    
    /**
     * @return string
     */
    public function infoPath(): string
    {
        return $this->infoPath;
    }
    
    
    /**
     * @return string
     */
    public function thumbnailPath(): string
    {
        return $this->thumbnailPath;
    }

    /**
     * @return string
     */
    public function popupPath(): string
    {
        return $this->popupPath;
    }

    /**
     * @return string
     */
    public function galleryPath()
    {
        return $this->galleryPath;
    }
}