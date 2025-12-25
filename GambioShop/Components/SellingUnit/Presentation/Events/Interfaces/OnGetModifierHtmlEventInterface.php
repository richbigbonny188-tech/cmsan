<?php
/*--------------------------------------------------------------------------------------------------
    OnGetModifierHtmlEventInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Presentation\Events\Interfaces;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

/**
 * Interface OnGetModifierHtmlEventInterface
 * @package Gambio\Shop\SellingUnit\Presentation\Events\Interfaces
 */
interface OnGetModifierHtmlEventInterface
{
    /**
     * @return SellingUnitId
     */
    public function sellingUnitId(): SellingUnitId;
    
    /**
     * @param string $html
     */
    public function appendHtml(string $html): void;
    
    /**
     * @return string
     */
    public function html(): string;
    
    /**
     * @return SelectedQuantity
     */
    public function selectedQuantity(): QuantityInterface;
    
    
    /**
     * @return SellingUnitStockInterface
     */
    public function sellingUnitStock(): SellingUnitStockInterface;
}