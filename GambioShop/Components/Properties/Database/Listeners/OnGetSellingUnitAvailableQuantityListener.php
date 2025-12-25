<?php
/*------------------------------------------------------------------------------
 OnGetSellingUnitAvailableQuantityListener.php 2021-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\Database\Listeners;

use Gambio\Core\Event\Abstracts\AbstractPrioritizedEventListener;
use Gambio\Shop\Properties\Database\Exceptions\IncompletePropertyListException;
use Gambio\Shop\Properties\Database\Exceptions\ProductDoesntHavePropertiesException;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertyQuantityReadServiceInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitAvailableQuantityEventInterface;
use Gambio\Shop\SellingUnit\Unit\Exceptions\InvalidQuantityException;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ExceptionStacker;

/**
 * Class OnGetSellingUnitAvailableQuantityListener
 * @package Gambio\Shop\Properties\Database\Listeners
 */
class OnGetSellingUnitAvailableQuantityListener extends AbstractPrioritizedEventListener
{
    public const PRIORITY = self::PRIORITY_NORMAL;
    
    /**
     * @var PropertyQuantityReadServiceInterface
     */
    protected $quantityService;
    
    
    /**
     * OnGetSellingUnitQuantityListener constructor.
     *
     * @param PropertyQuantityReadServiceInterface $service
     */
    public function __construct(PropertyQuantityReadServiceInterface $service)
    {
        $this->quantityService = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return self::PRIORITY_NORMAL;
    }
    
    
    /**
     * @param OnGetSellingUnitAvailableQuantityEventInterface $event
     *
     * @return OnGetSellingUnitAvailableQuantityEventInterface
     */
    public function __invoke(OnGetSellingUnitAvailableQuantityEventInterface $event
    ): OnGetSellingUnitAvailableQuantityEventInterface {
        try {
            $quantity = $this->quantityService->getAvailableQuantityBy($event->id(),
                                                                       $event->product(),
                                                                       $event->requested());
            if ($quantity) {
                $event->setMainQuantity($quantity, self::PRIORITY);
            }
        } catch (InvalidQuantityException $e) {
            //if the product have properties but there is no combination for the informed sellingUnitId
            $event->setMainQuantity(null, self::PRIORITY);
            $requested = $event->requested();
            if ($requested instanceof ExceptionStacker) {
                /** @var ExceptionStacker $requested */
                $requested->stackException($e);
            }
        } catch (IncompletePropertyListException $e) {
            //if the product have properties but the sellingUnit doesnt have all the properties
            $event->setMainQuantity(null, self::PRIORITY);
        } catch (ProductDoesntHavePropertiesException $e) {
            //does nothing
        }
        
        return $event;
    }
}
