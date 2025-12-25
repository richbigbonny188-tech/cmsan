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

namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO;

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
    private $alternateText;
    /**
     * @var string
     */
    private $infoUrl;
    /**
     * @var int
     */
    private $number;
    /**
     * @var string
     */
    private $popUpUrl;
    
    /**
     * @var string|null
     */
    protected $thumbNailUrl;
    /**
     * @var string|null
     */
    private $galleryUrl;


    /**
     * ImageDto constructor.
     *
     * @param string $relativePath
     * @param string|null $infoUrl
     * @param string|null $popUpUrl
     * @param string|null $thumbNailUrl
     * @param string $alternativeText
     * @param int $number
     * @param string|null $galleryUrl
     */
    public function __construct(
        string $relativePath,
        string $infoUrl = null,
        string $popUpUrl = null,
        string $thumbNailUrl = null,
        string $alternativeText,
        int $number,
        string $galleryUrl = null
    ) {
        $this->relativePath = $relativePath;
        $this->alternateText = $alternativeText;
        $this->number = $number;
        $this->infoUrl = $infoUrl ?? $relativePath;
        $this->popUpUrl = $popUpUrl ?? $relativePath;
        $this->thumbNailUrl = $thumbNailUrl;
        $this->galleryUrl = $galleryUrl ?? $relativePath;
    }


    /**
     * @return string
     */
    public function alternateText(): string
    {
        return $this->alternateText;
    }


    /**
     * @return string
     */
    public function infoUrl(): string
    {
        return $this->infoUrl;
    }


    /**
     * @return int
     */
    public function number(): int
    {
        return $this->number;
    }
    
    
    /**
     * @return string
     */
    public function popUpUrl(): string
    {
        return $this->popUpUrl;
    }
    
    
    /**
     * @inheritDoc
     */
    public function relativePath(): string
    {
        return $this->relativePath;
    }
    
    
    /**
     * @return string|null
     */
    public function thumbNailUrl(): ?string
    {
        return $this->thumbNailUrl;
    }

    /**
     * @return string|null
     */
    public function galleryUrl(): ?string
    {
        return $this->galleryUrl;
    }


}