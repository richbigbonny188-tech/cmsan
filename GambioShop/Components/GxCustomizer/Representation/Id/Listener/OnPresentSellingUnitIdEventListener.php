<?php
/**
 * OnPresentSellingUnitIdEventListener.php 2020-06-10
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\GxCustomizer\Representation\Id\Listener;

use Gambio\Shop\GxCustomizer\ProductModifiers\Database\ValueObjects\CustomizerModifierIdentifier;
use Gambio\Shop\GxCustomizer\Representation\Id\Factories\CustomizerPresentationIdFactory;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;

/**
 * Class OnPresentSellingUnitIdEventListener
 * @package Gambio\Shop\GxCustomizer\Representation\Id\Listener
 */
class OnPresentSellingUnitIdEventListener
{
    /**
     * @var CustomizerPresentationIdFactory
     */
    protected $factory;
    
    
    /**
     * OnPresentSellingUnitIdEventListener constructor.
     *
     * @param CustomizerPresentationIdFactory $factory
     */
    public function __construct(CustomizerPresentationIdFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param OnPresentSellingUnitIdEvent $event
     */
    public function __invoke(OnPresentSellingUnitIdEvent $event): void
    {
        $sellingUnitId  = $event->sellingUnitId();
        $modifiers      = $sellingUnitId->modifiers();
        $collection     = $event->presentationIdCollection();
        
        if (count($modifiers)) {
    
            foreach ($modifiers as $modifier) {
                
                if ($modifier instanceof CustomizerModifierIdentifier) {
                    
                    $collection[] = $this->factory->create($modifier);
                }
            }
        }
    }
}