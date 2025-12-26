<?php
/**
 * SellingUnitPresenter.php 2020-08-05
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation;

use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollection;
use Gambio\Shop\SellingUnit\Presentation\Collections\PresentationIdCollectionInterface;
use Gambio\Shop\SellingUnit\Presentation\Events\OnGetModifierHtmlEvent;
use Gambio\Shop\SellingUnit\Presentation\Events\OnGetSellingUnitProductLinkEvent;
use Gambio\Shop\SellingUnit\Presentation\Events\OnGetShortDescriptionEvent;
use Gambio\Shop\SellingUnit\Presentation\Events\OnPresentSellingUnitIdEvent;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ProductLink;
use Gambio\Shop\SellingUnit\Presentation\ValueObjects\ShortDescription;
use Gambio\Shop\SellingUnit\Unit\SellingUnitInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class SellingUnitPresenter
 * @package Gambio\Shop\SellingUnit\Presentation
 */
class SellingUnitPresenter implements SellingUnitPresenterInterface
{
    /**
     * @var SellingUnitInterface
     */
    protected $sellingUnit;
    
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    
    /**
     * @var PresentationIdCollectionInterface
     */
    protected $presentationIdCollection;
    
    /**
     * @var ProductLink
     */
    protected $productLink;
    
    /**
     * @var string
     */
    protected $modifierHtml;
    
    /**
     * @var ShortDescription
     */
    protected $shortDescription;
    
    
    /**
     * SellingUnitPresenter constructor.
     *
     * @param SellingUnitInterface     $sellingUnit
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SellingUnitInterface $sellingUnit, EventDispatcherInterface $dispatcher)
    {
        $this->sellingUnit = $sellingUnit;
        $this->dispatcher  = $dispatcher;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPresentationIdCollection(): PresentationIdCollectionInterface
    {
        if ($this->presentationIdCollection === null) {
    
            $collection = new PresentationIdCollection($this->sellingUnit->id()->productId());
            $event      = new OnPresentSellingUnitIdEvent($this->sellingUnit->id(), $collection);
            $this->dispatcher->dispatch($event);
            
            $this->presentationIdCollection = $event->presentationIdCollection();
        }
        
        return $this->presentationIdCollection;
    }
    
    
    /**
     * @return ProductLink
     */
    public function getProductLink(): ProductLink
    {
        if ($this->productLink === null) {
            
            $event = new OnGetSellingUnitProductLinkEvent($this->sellingUnit, $this->getPresentationIdCollection());
            $event = $this->dispatcher->dispatch($event);
            
            $this->productLink = $event->productLink();
        }
        
        return $this->productLink;
    }
    
    
    /**
     * @return string
     */
    public function getModifierHtml(): string
    {
        if ($this->modifierHtml === null) {
    
            $sellingUnitId    = $this->sellingUnit->id();
            /** @var SelectedQuantity $selectedQuantity */
            $selectedQuantity = $this->sellingUnit->selectedQuantity();
            $sellingUnitStock = $this->sellingUnit->stock();
            
            $event = new OnGetModifierHtmlEvent($sellingUnitId, $selectedQuantity, $sellingUnitStock);
            $event = $this->dispatcher->dispatch($event);
            
            $html = $event->html();
            
            if(strlen($html) > 0) {
                $this->modifierHtml = substr($html, 0, -6);
            }
            else {
                $this->modifierHtml = '';
            }
            
        }
    
        return $this->modifierHtml;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getShortDescription(): ShortDescription
    {
        if ($this->shortDescription === null) {
            
            $event = new OnGetShortDescriptionEvent($this->sellingUnit);
            $event = $this->dispatcher->dispatch($event);
            
            $this->shortDescription = $event->shortDescription();
        }
        
        return $this->shortDescription;
    }
}