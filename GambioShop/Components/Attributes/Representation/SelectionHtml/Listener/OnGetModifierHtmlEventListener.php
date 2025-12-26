<?php
/**
 * OnGetModifierHtmlEventListener.php 2020-3-19
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Attributes\Representation\SelectionHtml\Listener;

use Exception;
use Gambio\Shop\Attributes\Representation\SelectionHtml\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetModifierHtmlEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Class OnGetModifierHtmlEventListener
 * @package Gambio\Shop\Attributes\Representation\SelectionHtml\Listener
 */
class OnGetModifierHtmlEventListener
{
    /**
     * @var ReadServiceInterface
     */
    protected $readService;
    
    
    /**
     * OnGetModifierHtmlEventListener constructor.
     *
     * @param ReadServiceInterface $readService
     */
    public function __construct(ReadServiceInterface $readService)
    {
        $this->readService = $readService;
    }
    
    
    /**
     * @param OnGetModifierHtmlEventInterface $event
     */
    public function __invoke(OnGetModifierHtmlEventInterface $event)
    {
        $modifierIdentifier = $event->sellingUnitId()->modifiers();
        $languageId         = $event->sellingUnitId()->language();
        $selectedQuantity   = $event->selectedQuantity();
        $sellingUnitStock   = $event->sellingUnitStock();
        
        try {
            
            $generator = $this->readService->selectionHtmlGenerator($modifierIdentifier, $languageId, $selectedQuantity, $sellingUnitStock);
            $event->appendHtml($generator->toHtml());
        } catch (Exception $exception) {
            unset($exception);
            return;
        }
    }
}