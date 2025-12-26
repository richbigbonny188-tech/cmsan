<?php
/* --------------------------------------------------------------
  ProductQtyInput.php 2023-05-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters;

use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\DataOutput;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output\HtmlOutput;

/**
 * Class ProductQtyInput
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ProductQtyInput implements DataOutput, HtmlOutput
{
    private const INPUT_ID        = "gm_attr_calc_qty_%d";
    private const INPUT_ON_KEY_UP = "gm_calc_prices_listing('%d')";


    /**
     * @param int   $productId
     * @param float $productQty
     */
    public function __construct(
        private int   $productId,
        private float $productQty
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'NAME'    => 'products_qty',
            'VALUE'   => (string)$this->productQty,
            'SIZE'    => '3',
            'ID'      => $this->getInputId(),
            'ONKEYUP' => $this->getInputOnKeyup(),
            'CLASS'   => 'gm_listing_form gm_class_input',
            'TYPE'    => 'text',
        ];
    }


    /**
     * @return string
     */
    private function getInputId(): string
    {
        return sprintf(self::INPUT_ID, $this->productId);
    }


    /**
     * @return string
     */
    private function getInputOnKeyup(): string
    {
        return sprintf(self::INPUT_ON_KEY_UP, $this->productId);
    }


    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        return sprintf(
            '<input type="text" name="products_qty" value="%s" class="gm_listing_form gm_class_input" size="3" id="%s" onkeyup="%s" />',
            $this->productQty,
            $this->getInputId(),
            $this->getInputOnKeyup(),
        );
    }
}