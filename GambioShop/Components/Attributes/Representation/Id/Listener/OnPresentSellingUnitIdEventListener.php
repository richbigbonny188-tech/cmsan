<?php
/*--------------------------------------------------------------------
 OnPresentSellingUnitIdEventListener.php 2020-3-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\Id\Listener;

use Gambio\Shop\Attributes\Representation\Id\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;

/**
 * Class OnPresentSellingUnitIdEventListener
 * @package Gambio\Shop\SellingUnit\Representation\Listener
 */
class OnPresentSellingUnitIdEventListener
{
    /**
     * @var ReadServiceInterface
     */
    protected $readService;
    
    
    /**
     * OnPresentSellingUnitIdEventListener constructor.
     *
     * @param ReadServiceInterface $readService
     */
    public function __construct(ReadServiceInterface $readService)
    {
        $this->readService = $readService;
    }
    
    
    /**
     * @param OnPresentSellingUnitIdEvent $event
     */
    public function __invoke(OnPresentSellingUnitIdEvent $event)
    {
        $sellingUnitId  = $event->sellingUnitId();
        $modifiers      = $sellingUnitId->modifiers();
        $productId      = $sellingUnitId->productId();
        $collection     = $event->presentationIdCollection();
        
        $this->readService->extendPresentationIdCollection($collection, $modifiers, $productId);
    }
}