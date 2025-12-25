<?php
/**
 * OnGetSellingUnitProductLinkEventListener.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ProductLink\Listener;

use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetSellingUnitProductLinkEventInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ProductLink;

/**
 * Class OnGetSellingUnitProductLinkEventListener
 * @package Gambio\Shop\Product\Representation\ProductLink\Listener
 */
class OnGetSellingUnitProductLinkEventListener
{
    /**
     * @param OnGetSellingUnitProductLinkEventInterface $event
     */
    public function __invoke(OnGetSellingUnitProductLinkEventInterface $event)
    {
        $sellingUnit    = $event->sellingUnit();
        $presentationId = $event->presentationId();
        $productName    = $sellingUnit->productInfo()->name();
        $productLink    = xtc_href_link(FILENAME_PRODUCT_INFO,
                                        xtc_product_link((string)$presentationId, $productName->value())
                                        . '&no_boost=1');
        
        $event->setProductLink($this->createProductLink($productLink));
    }
    
    
    /**
     * @param string $link
     *
     * @return ProductLink
     */
    protected function createProductLink(string $link): ProductLink
    {
        return new ProductLink($link);
    }
}