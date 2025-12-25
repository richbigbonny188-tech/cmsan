<?php
/*------------------------------------------------------------------------------
 OnGetSellingUnitEanEventListener.php 2021-06-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Properties\Database\Listeners;

use Gambio\Core\Event\Abstracts\AbstractPrioritizedEventListener;
use Gambio\Shop\Properties\Database\Services\Interfaces\PropertiesReaderServiceInterface;
use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitEanEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Ean;

/**
 * Class OnGetSellingUnitEanEventListener
 * @package Gambio\Shop\Properties\SellingUnitEan\Listener
 */
class OnGetSellingUnitEanEventListener extends AbstractPrioritizedEventListener
{
    public const PRIORITY = self::PRIORITY_LOW;
    
    /**
     * @var PropertiesReaderServiceInterface
     */
    protected $service;
    
    
    /**
     * OnGetSellingUnitEanEventListener constructor.
     *
     * @param PropertiesReaderServiceInterface $service
     */
    public function __construct(PropertiesReaderServiceInterface $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return self::PRIORITY_LOW;
    }
    
    
    /**
     * @param OnGetSellingUnitEanEventInterface $event
     */
    public function __invoke(OnGetSellingUnitEanEventInterface $event)
    {
        $combination = $this->service->getCombinationFor($event->id());
        if ($combination && $combination->ean()->value()) {
            $event->builder()->wipeData()->withEanAtPos(new Ean($combination->ean()->value()), 1000);
        }
    }
}