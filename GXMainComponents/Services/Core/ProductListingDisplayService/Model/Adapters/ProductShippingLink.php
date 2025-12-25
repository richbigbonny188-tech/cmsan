<?php
/* --------------------------------------------------------------
  ProductShippingLink.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;

/**
 * Class ProductShippingLink
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ProductShippingLink implements HtmlOutput
{
    /**
     * @param string $shippingUrl
     * @param string $shippingExcludedText
     * @param string $shippingCostText
     * @param string $shippingIsFreeText
     * @param bool   $shippingIsEnabled
     * @param bool   $hasFreeShipping
     */
    public function __construct(
        private string $shippingUrl,
        private string $shippingExcludedText,
        private string $shippingCostText,
        private string $shippingIsFreeText,
        private bool   $shippingIsEnabled,
        private bool   $hasFreeShipping
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        if (!$this->shippingIsEnabled) {
            return '';
        }

        if ($this->hasFreeShipping) {
            return ' ' . $this->shippingIsFreeText;
        }

        return sprintf(
            ' %s <a class="gm_shipping_link lightbox_iframe"
                            href="%s"
                            target="_self"
						    rel="nofollow"
						    data-modal-settings=\'{"title":"%s", "sectionSelector": ".content_text", "bootstrapClass": "modal-lg"}\'>
					<span style="text-decoration:underline">%s</span>
					</a>',
            $this->shippingExcludedText,
            $this->shippingUrl,
            $this->shippingCostText,
            $this->shippingCostText
        );
    }
}