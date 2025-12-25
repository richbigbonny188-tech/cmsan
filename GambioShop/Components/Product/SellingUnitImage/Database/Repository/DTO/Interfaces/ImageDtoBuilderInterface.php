<?php
/*--------------------------------------------------------------------
 ImageDtoBuilderInterface.php 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/


namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\Interfaces;


use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\ImageDto;

interface ImageDtoBuilderInterface
{

    /**
     * @param string $relativePath
     *
     * @return ImageDtoBuilderInterface
     */
    public function withRelativePath(string $relativePath): ImageDtoBuilderInterface;

    /**
     * @param string $infoUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withInfoUrl(string $infoUrl): ImageDtoBuilderInterface;

    /**
     * @param string $popUpUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withPopUpUrl(string $popUpUrl): ImageDtoBuilderInterface;

    /**
     * @param string $thumbnailUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withThumbnailUrl(string $thumbnailUrl): ImageDtoBuilderInterface;

    /**
     * @param string $alternativeText
     *
     * @return ImageDtoBuilderInterface
     */
    public function withAlternativeText(string $alternativeText): ImageDtoBuilderInterface;

    /**
     * @param string $number
     *
     * @return ImageDtoBuilderInterface
     */
    public function withNumber(int $number): ImageDtoBuilderInterface;

    /**
     * @param string $galleryUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withGalleryUrl(string $galleryUrl): ImageDtoBuilderInterface;

    /**
     * @return ImageDto
     */
    public function build() : ImageDto;

}