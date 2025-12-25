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

namespace Gambio\Shop\Attributes\SellingUnitImages\Database\Listener;

use Gambio\Shop\Attributes\ProductModifiers\Database\ValueObjects\AttributeModifierIdentifier;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEventInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\Interfaces\ReadServiceInterface;
use Gambio\Shop\Attributes\SellingUnitImages\Database\ReadService;

class OnImageCollectionCreateEventListener
{
    /**
     * @var ReadService
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
    public function __invoke(OnImageCollectionCreateEventInterface $event) : void
    {
        foreach ($event->id()->modifiers() as $modifier) {
            if ($modifier instanceof AttributeModifierIdentifier) {
                $images = $this->service->getAttributeOptionImagesByProductId(
                    $modifier,
                    $event->id()->productId(),
                    $event->id()->language()
                );
                $event->builder()->withImages($images);
            }
        }
    }
}