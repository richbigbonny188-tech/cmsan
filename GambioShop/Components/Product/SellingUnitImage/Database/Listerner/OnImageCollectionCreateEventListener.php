<?php
/*--------------------------------------------------------------------------------------------------
    OnImageCollectionCreateEventListener.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Listerner;

use Gambio\Shop\Product\SellingUnitImage\Database\Service\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEventInterface;

class OnImageCollectionCreateEventListener
{
    /**
     * @var ReadServiceInterface
     */
    private $service;
    
    
    /**
     * OnCollectionCreateEventListener constructor.
     *
     * @param ReadServiceInterface $service
     */
    public function __construct(ReadServiceInterface $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @param OnImageCollectionCreateEventInterface $event
     */
    public function __invoke(OnImageCollectionCreateEventInterface $event): void
    {
        $image = $this->service->mainProductImage($event->id()->productId(),
                                                  $event->id()->language());
        if ($image) {
            $event->builder()->withImage($image);
        }
        $event->builder()->withImages($this->service->getProductImages($event->id()->productId(),
                                                                       $event->id()->language()));
    }
    
}