<?php
/* --------------------------------------------------------------
  ProductAddToCartForm.php 2023-05-25
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
 * Class ProductAddToCartForm
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\Adapters
 */
class ProductAddToCartForm implements DataOutput, HtmlOutput
{
    use HtmlUtilityTrait;

    private const FORM_ID     = 'gm_add_to_cart_%d';
    private const FORM_METHOD = 'post';
    private const FORM_SUBMIT = 'return gm_quantity_check_listing(\'%d\')';


    /**
     * @param int    $productId
     * @param string $actionUrl
     */
    public function __construct(
        private int    $productId,
        private string $actionUrl
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'ID'         => $this->getFormId(),
            'ACTION_URL' => $this->actionUrl,
            'METHOD'     => self::FORM_METHOD,
            'ONSUBMIT'   => $this->getFormOnSubmit(),
        ];
    }


    /**
     * @return string
     */
    private function getFormId(): string
    {
        return sprintf(self::FORM_ID, $this->productId);
    }


    /**
     * @return string
     */
    private function getFormOnSubmit(): string
    {
        return sprintf(self::FORM_SUBMIT, $this->productId);
    }


    /**
     * @inheritDoc
     */
    public function toHtml(): string
    {
        $html = $this->prepareHtmlAttributes(
            [
                'id'       => $this->getFormId(),
                'action'   => $this->actionUrl,
                'method'   => self::FORM_METHOD,
                'onsubmit' => $this->getFormOnSubmit(),
            ]
        );

        return "<form $html>";
    }
}