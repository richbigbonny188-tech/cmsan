<?php
/**
 * OnGetModifierHtmlEventListener.php 2020-3-17
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\Representation\SelectionHtml\Listener;

use Gambio\Shop\Properties\Representation\SelectionHtml\Exceptions\InvalidValueIdsSpecifiedException;
use Gambio\Shop\Properties\Representation\SelectionHtml\ReadServiceInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetModifierHtmlEventInterface;

/**
 * Class OnGetModifierHtmlEventListener
 * @package Gambio\Shop\Properties\Representation\SelectionHtml\Listener
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
        $modifiers  = $event->sellingUnitId()->modifiers();
        $languageId = $event->sellingUnitId()->language();
    
        try {
            $generator = $this->readService->selectionHtmlGenerator($modifiers, $languageId);
            $event->appendHtml($generator->toHtml());
        } catch (InvalidValueIdsSpecifiedException $exception) {
            unset($exception);
            return;
        }
    }
}