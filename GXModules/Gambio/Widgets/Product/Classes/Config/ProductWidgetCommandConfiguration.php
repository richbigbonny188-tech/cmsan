<?php
/**
 * ProductWidgetCommandConfiguration.php 2020-3-31
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * Class ProductWidgetCommandConfiguration
 */
class ProductWidgetCommandConfiguration
{

    /**
     * @var string
     */
    protected $elementId = '';

    /**
     * @var string
     */
    protected $elementClassName = '';

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
     * @param string $elementId
     * @param string $elementClassName
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
        ?string $elementId,
        ?string $elementClassName,
        IdType $languageId,
        LanguageCode $languageCode,
        string $truncate,
        bool $hoverable,
        string $showManufacturerImages,
        string $showProductRibbons,
        bool $fullscreenPage,
        bool $showRating
    ) {

        if ($elementId) {
            $this->elementId = $elementId;
        }

        if ($elementClassName) {
            $this->elementClassName = $elementClassName;
        }

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
     * @return bool
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