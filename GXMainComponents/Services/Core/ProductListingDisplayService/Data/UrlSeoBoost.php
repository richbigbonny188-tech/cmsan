<?php
/* --------------------------------------------------------------
  SeoBoost.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use GMSEOBoost_ORIGIN;

/**
 * Class SeoBoost
 *
 * @package            Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 * @codeCoverageIgnore A wrapper around GMSEOBoost class as it is the only place using legacy code in the module.
 */
class UrlSeoBoost
{
    /**
     * @param GMSEOBoost_ORIGIN $gmSeoBoost
     */
    public function __construct(private GMSEOBoost_ORIGIN $gmSeoBoost)
    {
    }


    /**
     * @param int      $productId
     * @param string   $productName
     * @param int|null $languageId
     * @param string   $productUrlKeywords
     *
     * @return string
     */
    public function getProductUrl(
        int    $productId,
        string $productName,
        ?int   $languageId = null,
        string $productUrlKeywords = ''
    ): string
    {
        if (!is_int($languageId)) {
            $languageId = false;
        }


        return $this->gmSeoBoost->get_boosted_product_url($productId, $productName, $languageId, $productUrlKeywords);
    }


    /**
     * @param int      $contentId
     * @param int|null $languageId
     * @param string   $itemName
     *
     * @return string
     */
    public function getContentUrl(
        int    $contentId,
        ?int   $languageId = null,
        string $itemName = ''
    ): string
    {
        if (!is_int($languageId)) {
            $languageId = false;
        }

        return $this->gmSeoBoost->get_boosted_content_url($contentId, $languageId, $itemName);
    }
}