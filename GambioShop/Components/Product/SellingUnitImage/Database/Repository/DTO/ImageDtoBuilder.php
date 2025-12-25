<?php
/*--------------------------------------------------------------------
 ImageDtoBuilder.php 2020-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/


namespace Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO;


use Gambio\Shop\Product\SellingUnitImage\Database\Repository\DTO\Interfaces\ImageDtoBuilderInterface;

class ImageDtoBuilder implements ImageDtoBuilderInterface
{
    /**
     * @var string
     */
    protected $relativePath;

    /**
     * @var string
     */
    protected $infoUrl;

    /**
     * @var string
     */
    protected $popUpUrl;

    /**
     * @var string
     */
    protected $thumbnailUrl;

    /**
     * @var string
     */
    protected $alternativeText;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $galleryUrl;

    /**
     * @param string $relativePath
     *
     * @return ImageDtoBuilderInterface
     */
    public function withRelativePath(string $relativePath): ImageDtoBuilderInterface
    {
        $this->relativePath = $relativePath;
        return $this;
    }

    /**
     * @param string $infoUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withInfoUrl(string $infoUrl): ImageDtoBuilderInterface
    {
        $this->infoUrl = $infoUrl;
        return $this;
    }

    /**
     * @param string $popUpUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withPopUpUrl(string $popUpUrl): ImageDtoBuilderInterface
    {
        $this->popUpUrl = $popUpUrl;
        return $this;
    }

    /**
     * @param string $thumbnailUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withThumbnailUrl(string $thumbnailUrl): ImageDtoBuilderInterface
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    /**
     * @param string $alternativeText
     *
     * @return ImageDtoBuilderInterface
     */
    public function withAlternativeText(string $alternativeText): ImageDtoBuilderInterface
    {
        $this->alternativeText = $alternativeText;
        return $this;
    }

    /**
     * @param int $number
     *
     * @return ImageDtoBuilderInterface
     */
    public function withNumber(int $number): ImageDtoBuilderInterface
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @param string $galleryUrl
     *
     * @return ImageDtoBuilderInterface
     */
    public function withGalleryUrl(string $galleryUrl): ImageDtoBuilderInterface
    {
        $this->galleryUrl = $galleryUrl;
        return $this;
    }

    /**
     * reset the internal variables
     */
    public function reset()
    {
        $this->relativePath    = null;
        $this->infoUrl         = null;
        $this->popUpUrl        = null;
        $this->thumbnailUrl    = null;
        $this->alternativeText = null;
        $this->number          = null;
        $this->galleryUrl      = null;
    }

    /**
     * ImageDtoBuilder constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return ImageDto
     */
    public function build(): ImageDto
    {
        $result = new ImageDto(
            $this->relativePath,
            $this->infoUrl,
            $this->popUpUrl,
            $this->thumbnailUrl,
            $this->alternativeText,
            $this->number,
            $this->galleryUrl
        );
        $this->reset();
        return $result;
    }


}