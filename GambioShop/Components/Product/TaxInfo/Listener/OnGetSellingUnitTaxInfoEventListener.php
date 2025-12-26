<?php
/**
 * OnGetSellingUnitTaxInfoEventListener.php 2020-3-23
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\Product\TaxInfo\Listener;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetSellingUnitTaxInfoEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\TaxInfo;
use main_ORIGIN;

/**
 * Class OnGetSellingUnitTaxInfoEventListener
 * @package Gambio\Shop\Product\TaxInfo\Listener
 */
class OnGetSellingUnitTaxInfoEventListener
{
    /**
     * @var main_ORIGIN
     */
    protected $main;
    
    
    /**
     * OnGetSellingUnitTaxInfoEventListener constructor.
     *
     * @param main_ORIGIN $main
     */
    public function __construct(main_ORIGIN $main)
    {
        $this->main = $main;
    }
    
    
    /**
     * @param OnGetSellingUnitTaxInfoEventInterface $event
     */
    public function __invoke(OnGetSellingUnitTaxInfoEventInterface $event)
    {
        $taxClassId = $event->product()->getTaxClassId();
        
        if ($taxClassId > 0) {
            $taxRate    = $event->xtcPrice()->getTaxRateByTaxClassId($taxClassId);
            $taxInfo    = $this->main->getTaxInfo($taxRate) ?? '';
            $valueObject = new TaxInfo($taxInfo, (float)$taxRate, $taxClassId);
        } else {
            $valueObject = new TaxInfo('', 0, $taxClassId);
        }
        
        $event->setTaxInfo($valueObject);
    }
}