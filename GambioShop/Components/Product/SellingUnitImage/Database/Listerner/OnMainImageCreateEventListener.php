<?php
/*--------------------------------------------------------------------------------------------------
    OnMainImageCreateEventListener.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\SellingUnitImage\Database\Listerner;

use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEventInterface;
use Gambio\Shop\Product\SellingUnitImage\Database\Service\ReadService;

class OnMainImageCreateEventListener
{
    /**
     * @var ReadService
     */
    private $service;
    
    
    /**
     * OnCollectionCreateEventListener constructor.
     *
     * @param ReadService $service
     */
    public function __construct( ReadService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @param OnImageCollectionCreateEventInterface $event
     */
    public function __invoke(OnImageCollectionCreateEventInterface $event): void
    {
        $image = $this->service->mainProductImage($event->id()->productId(), $event->id()->language());
        if($image){
            $event->builder()->withImage($image);
        }
    }
    
}