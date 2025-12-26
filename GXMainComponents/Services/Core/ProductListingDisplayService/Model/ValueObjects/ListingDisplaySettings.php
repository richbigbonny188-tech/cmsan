<?php
/* --------------------------------------------------------------
  ListingDisplaySettings.php 2023-12-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Seo;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Visibility;

/**
 * Class ListingDisplaySettings
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects
 */
class ListingDisplaySettings
{
    /**
     * @param Locale     $locale
     * @param Seo        $seo
     * @param Visibility $visibility
     */
    public function __construct(
        private Locale     $locale,
        private Seo        $seo,
        private Visibility $visibility
    ) {
    }
    
    
    /**
     * @return Locale
     */
    public function locale(): Locale
    {
        return $this->locale;
    }
    
    
    /**
     * @return Seo
     */
    public function seo(): Seo
    {
        return $this->seo;
    }
    
    
    /**
     * @return Visibility
     */
    public function visibility(): Visibility
    {
        return $this->visibility;
    }
}