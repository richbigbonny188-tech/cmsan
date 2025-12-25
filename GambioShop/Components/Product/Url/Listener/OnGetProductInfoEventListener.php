<?php
/*--------------------------------------------------------------------
 OnGetProductInfoEventListener.php 2020-2-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Product\Url\Listener;

use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductInfoEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetProductUrlEventInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\OnGetProductUrlEvent;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Url;

/**
 * Class OnGetProductInfoEventListener
 * @package Gambio\Shop\Product\Url\Listener
 */
class OnGetProductInfoEventListener
{
    /**
     * @param OnGetProductInfoEventInterface $event
     */
    public function __invoke(OnGetProductInfoEventInterface $event)
    {
        $product   = $event->product();
        $productId = $event->productId();
        $url       = $this->createUrl($product->getProductsUrl(), $productId);
        
        $event->builder()->withUrl($url);
        $event->dispatcher()->dispatch($this->createOnGetProductUrlEvent($event));
    }
    
    
    /**
     * @param string|null $url
     *
     * @param ProductId   $productId
     *
     * @return Url
     */
    protected function createUrl(?string $url, ProductId $productId): Url
    {
        if ($url !== null && defined('TEXT_MORE_INFORMATION') && defined('FILENAME_REDIRECT')) {
            
            $url = sprintf(TEXT_MORE_INFORMATION,
                           xtc_href_link(FILENAME_REDIRECT,
                                         'action=product&id=' . $productId->value(),
                                         'NONSSL',
                                         true));
        }
        
        return new Url($url);
    }
    
    
    /**
     * @param OnGetProductInfoEventInterface $event
     *
     * @return OnGetProductUrlEventInterface
     */
    protected function createOnGetProductUrlEvent(OnGetProductInfoEventInterface $event): OnGetProductUrlEventInterface
    {
        return new OnGetProductUrlEvent($event->builder(), $event->product(), $event->productId());
    }
}