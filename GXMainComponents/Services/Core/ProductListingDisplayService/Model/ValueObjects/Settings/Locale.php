<?php
/* --------------------------------------------------------------
  Locale.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale\Currency;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale\Language;

/**
 * Class Locale
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings
 */
class Locale
{
    /**
     * @param Language $language
     * @param Currency $currency
     */
    public function __construct(
        private Language $language,
        private Currency $currency
    )
    {
    }


    /**
     * @return Language
     */
    public function language(): Language
    {
        return $this->language;
    }


    /**
     * @return Currency
     */
    public function currency(): Currency
    {
        return $this->currency;
    }
}