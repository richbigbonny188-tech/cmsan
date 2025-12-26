<?php
/**
 * ProductListWidgetCommandConfiguration.php 2023-11-29
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2023 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * Class ProductListWidgetCommandConfiguration
 */
class ProductListWidgetCommandConfiguration
{
    
    public const MAX_PRODUCTS_LIMIT = 50;

    /**
     * @var int
     */
    protected $categoryId;

    /**
     * @var string
     */
    protected $listType;

    /**
     * @var bool
     */
    protected $random;

    /**
     * @var string
     */
    protected $itemsPerRowXs;

    /**
     * @var string
     */
    protected $itemsPerRowSm;

    /**
     * @var string
     */
    protected $itemsPerRowMd;

    /**
     * @var string
     */
    protected $itemsPerRowLg;

    /**
     * @var int
     */
    protected $maxProducts;

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $elementClassName;

    /**
     * @var IdType
     */
    protected $languageId;

    /**
     * @var LanguageCode
     */
    protected $languageCode;

    /**
     * @var string
     */
    protected $presentation;

    /**
     * @var string
     */
    protected $truncate;

    /**
     * @var bool
     */
    protected $hoverable;

    /**
     * @var string
     */
    protected $showManufacturerImages;

    /**
     * @var string
     */
    protected $showProductRibbons;

    /**
     * @var bool
     */
    protected $fullscreenPage;

    /**
     * @var bool
     */
    protected $showRating;

    /**
     * CommandConfiguration constructor.
     * @param int $categoryId
     * @param string $listType
     * @param bool $random
     * @param string $itemsPerRowXs
     * @param string $itemsPerRowSm
     * @param string $itemsPerRowMd
     * @param string $itemsPerRowLg
     * @param int $maxProducts
     * @param string $elementId
     * @param string $elementClassName
     * @param string $presentation
     * @param IdType $languageId
     * @param LanguageCode $languageCode
     * @param string $truncate
     * @param bool $hoverable
     * @param string $showManufacturerImages
     * @param string $showProductRibbons
     * @param bool $fullscreenPage
     * @param bool $showRating
     */
    public function __construct(
        int $categoryId,
        string $listType,
        bool $random,
        string $itemsPerRowXs,
        string $itemsPerRowSm,
        string $itemsPerRowMd,
        string $itemsPerRowLg,
        int $maxProducts,
        string $elementId,
        string $elementClassName,
        string $presentation,
        IdType $languageId,
        LanguageCode $languageCode,
        string $truncate,
        bool $hoverable,
        string $showManufacturerImages,
        string $showProductRibbons,
        bool $fullscreenPage,
        bool $showRating
    ) {
        $this->categoryId = $categoryId;
        $this->listType = $listType;
        $this->maxProducts = $maxProducts;
        $this->elementId = $elementId;
        $this->elementClassName = $elementClassName;
        $this->presentation = $presentation;
        $this->random = $random;
        $this->itemsPerRowXs = $itemsPerRowXs;
        $this->itemsPerRowSm = $itemsPerRowSm;
        $this->itemsPerRowMd = $itemsPerRowMd;
        $this->itemsPerRowLg = $itemsPerRowLg;
        $this->languageId = $languageId;
        $this->languageCode = $languageCode;
        $this->truncate = $truncate;
        $this->hoverable = $hoverable;
        $this->showManufacturerImages = $showManufacturerImages;
        $this->showProductRibbons = $showProductRibbons;
        $this->fullscreenPage = $fullscreenPage;
        $this->showRating = $showRating;
    }

    /**
     * @return int
     */
    public function categoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function listType(): string
    {
        return $this->listType;
    }

    /**
     * @return bool
     */
    public function random(): bool
    {
        return $this->random;
    }

    /**
     * @return string
     */
    public function itemsPerRowXs(): string
    {
        return $this->itemsPerRowXs;
    }

    /**
     * @return string
     */
    public function itemsPerRowSm(): string
    {
        return $this->itemsPerRowSm;
    }

    /**
     * @return string
     */
    public function itemsPerRowMd(): string
    {
        return $this->itemsPerRowMd;
    }

    /**
     * @return string
     */
    public function itemsPerRowLg(): string
    {
        return $this->itemsPerRowLg;
    }

    /**
     * @return int
     */
    public function maxProducts(): int
    {
        return min($this->maxProducts, self::MAX_PRODUCTS_LIMIT);
    }

    /**
     * @return string
     */
    public function elementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function elementClassName(): string
    {
        return $this->elementClassName;
    }

    /**
     * @return IdType
     */
    public function languageId(): IdType
    {
        return $this->languageId;
    }

    /**
     * @return LanguageCode
     */
    public function languageCode(): LanguageCode
    {
        return $this->languageCode;
    }

    /**
     * @return string
     */
    public function presentation(): string
    {
        return $this->presentation;
    }

    /**
     * @return string
     */
    public function truncate(): string
    {
        return $this->truncate;
    }

    /**
     * @return bool
     */
    public function hoverable(): bool
    {
        return $this->hoverable;
    }

    /**
     * @return string
     */
    public function showManufacturerImages(): string
    {
        return $this->showManufacturerImages;
    }

    /**
     * @return string
     */
    public function showProductRibbons(): string
    {
        return $this->showProductRibbons;
    }

    /**
     * @return bool
     */
    public function fullscreenPage(): bool
    {
        return $this->fullscreenPage;
    }

    /**
     * @return bool
     */
    public function showRating(): bool
    {
        return $this->showRating;
    }
}