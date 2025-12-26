<?php
/* --------------------------------------------------------------
  Visibility.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings;

/**
 * Class Visibility
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings
 */
class Visibility
{
    public function __construct(
        private int  $thumbnailWidth,
        private int  $thumbnailHeight,
        private bool $showPrices,
        private bool $showShipping,
        private bool $shippingIsActive,
        private bool $lightboxIsActive
    )
    {
    }


    /**
     * @return int
     */
    public function thumbnailWidth(): int
    {
        return $this->thumbnailWidth;
    }


    /**
     * @return int
     */
    public function thumbnailHeight(): int
    {
        return $this->thumbnailHeight;
    }


    /**
     * @return bool
     */
    public function showPrices(): bool
    {
        return $this->showPrices;
    }


    /**
     * @return bool
     */
    public function showShipping(): bool
    {
        return $this->showShipping;
    }


    /**
     * @return bool
     */
    public function shippingIsActive(): bool
    {
        return $this->shippingIsActive;
    }


    /**
     * @return bool
     */
    public function lightboxIsActive(): bool
    {
        return $this->lightboxIsActive;
    }
}