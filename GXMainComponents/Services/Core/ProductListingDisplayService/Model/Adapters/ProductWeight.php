<?php
/* --------------------------------------------------------------
  ProductWeight.php 2023-12-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\Settings\Locale;

/**
 * Class ProductWeight
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ProductWeight implements HtmlOutput
{
    /**
     * @param float  $weight
     * @param Locale $locale
     */
    public function __construct(private float $weight, private Locale $locale)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        return str_replace(
            '.',
            $this->locale->currency()->decimalSeparator(),
            (string)$this->weight
        );
    }
}