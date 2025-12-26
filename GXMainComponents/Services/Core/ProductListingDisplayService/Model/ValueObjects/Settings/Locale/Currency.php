<?php
/* --------------------------------------------------------------
  Currency.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;

/**
 * Class Currency
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale
 */
class Currency
{
    /**
     * @param string $code
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @param int    $decimals
     */
    public function __construct(
        private string $code,
        private string $decimalSeparator,
        private string $thousandsSeparator,
        private int    $decimals
    )
    {
    }


    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }


    /**
     * @return string
     */
    public function decimalSeparator(): string
    {
        return $this->decimalSeparator;
    }


    /**
     * @return string
     */
    public function thousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }


    /**
     * @return int
     */
    public function decimals(): int
    {
        return $this->decimals;
    }
}