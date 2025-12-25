<?php
/*--------------------------------------------------------------------------------------------------
    OnGetModifierHtmlEvent.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\SellingUnit\Presentation\Events\Interfaces\OnGetModifierHtmlEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Class OnGetModifierHtmlEvent
 * @package Gambio\Shop\SellingUnit\Presentation\Events
 */
class OnGetModifierHtmlEvent implements OnGetModifierHtmlEventInterface
{
    /**
     * @var SellingUnitId
     */
    protected $sellingUnitId;
    
    /**
     * @var LanguageId
     */
    protected $languageId;
    
    /**
     * @var string
     */
    protected $html = '';
    
    /**
     * @var SelectedQuantity
     */
    protected $selectedQuantity;
    
    /**
     * @var SellingUnitStockInterface
     */
    protected $sellingUnitStock;
    
    
    /**
     * OnGetModifierHtmlEvent constructor.
     *
     * @param SellingUnitId             $sellingUnitId
     * @param QuantityInterface          $selectedQuantity
     * @param SellingUnitStockInterface $sellingUnitStock
     */
    public function __construct(
        SellingUnitId $sellingUnitId,
        QuantityInterface $selectedQuantity,
        SellingUnitStockInterface $sellingUnitStock
    ) {
        $this->sellingUnitId    = $sellingUnitId;
        $this->selectedQuantity = $selectedQuantity;
        $this->sellingUnitStock = $sellingUnitStock;
    }
    
    
    
    /**
     * @inheritDoc
     */
    public function sellingUnitId(): SellingUnitId
    {
        return $this->sellingUnitId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function appendHtml(string $html): void
    {
        $this->html .= $html;
    }
    
    
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->html;
    }
    
    
    /**
     * @inheritDoc
     */
    public function selectedQuantity(): QuantityInterface
    {
        return $this->selectedQuantity;
    }
    
    
    /**
     * @inheritDoc
     */
    public function sellingUnitStock(): SellingUnitStockInterface
    {
        return $this->sellingUnitStock;
    }
}