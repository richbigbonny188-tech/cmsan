<?php
/**
 * OnGetShortDescriptionEventListener.php 2020-3-18
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\Representation\ShortDescription\Listener;

use Gambio\Shop\Product\Representation\ShortDescription\Exceptions\ShortDescriptionNotFoundException;
use Gambio\Shop\Product\Representation\ShortDescription\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetShortDescriptionEventInterface;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;

/**
 * Class OnGetShortDescriptionEventListener
 * @package Gambio\Shop\Product\Representation\ShortDescription\Listener
 */
class OnGetShortDescriptionEventListener
{
    /**
     * @var ReadServiceInterface
     */
    protected $readService;
    
    
    /**
     * OnGetShortDescriptionEventListener constructor.
     *
     * @param ReadServiceInterface $readService
     */
    public function __construct(ReadServiceInterface $readService)
    {
        $this->readService = $readService;
    }
    
    
    /**
     * @param OnGetShortDescriptionEventInterface $event
     */
    public function __invoke(OnGetShortDescriptionEventInterface $event)
    {
        $sellingUnit   = $event->getSellingUnit();
        $sellingUnitId = $sellingUnit->id();
        
        try {
            $shortDescription = $this->readService->shortDescription($sellingUnitId);
        } catch (ShortDescriptionNotFoundException $exception) {
            unset($exception);
            $shortDescription = new ShortDescription('');
        } finally {
            $event->setShortDescription($shortDescription);
        }
    }
}