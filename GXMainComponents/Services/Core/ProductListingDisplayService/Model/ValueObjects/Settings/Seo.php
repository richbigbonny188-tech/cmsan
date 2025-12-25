<?php
/* --------------------------------------------------------------
  Seo.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings;

/**
 * Class Seo
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings
 */
class Seo
{
    /**
     * @param bool $boostProduct
     * @param bool $boostContent
     * @param bool $boostShortUrls
     * @param bool $useFriendlyUrls
     * @param bool $useBoosterLanguage
     * @param bool $suppressIndexUsage
     */
    public function __construct(
        private bool $boostProduct,
        private bool $boostContent,
        private bool $boostShortUrls,
        private bool $useFriendlyUrls,
        private bool $useBoosterLanguage,
        private bool $suppressIndexUsage
    )
    {
    }


    /**
     * @return bool
     */
    public function boostProduct(): bool
    {
        return $this->boostProduct;
    }


    /**
     * @return bool
     */
    public function boostContent(): bool
    {
        return $this->boostContent;
    }


    /**
     * @return bool
     */
    public function boostShortUrls(): bool
    {
        return $this->boostShortUrls;
    }


    /**
     * @return bool
     */
    public function useFriendlyUrls(): bool
    {
        return $this->useFriendlyUrls;
    }


    /**
     * @return bool
     */
    public function useBoosterLanguage(): bool
    {
        return $this->useBoosterLanguage;
    }


    /**
     * @return bool
     */
    public function suppressIndexUsage(): bool
    {
        return $this->suppressIndexUsage;
    }
}