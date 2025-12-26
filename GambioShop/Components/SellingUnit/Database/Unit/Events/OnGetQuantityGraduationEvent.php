<?php
/*--------------------------------------------------------------------------------------------------
    OnGetQuantityGraduationEvent.php 2020-02-25
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\SellingUnit\Database\Unit\Events;

use Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces\OnGetQuantityGraduationEventInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\QuantityGraduation;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;
use ProductDataInterface;

class OnGetQuantityGraduationEvent implements OnGetQuantityGraduationEventInterface
{
    /**
     * @var SellingUnitId
     */
    private $id;
    /**
     * @var ProductDataInterface
     */
    private $product;
    /**
     * @var QuantityGraduation
     */
    private $quantityGraduation;
    
    
    /**
     * OnGetQuantityGraduationEvent constructor.
     *
     * @param SellingUnitId        $id
     * @param ProductDataInterface $product
     * @param QuantityGraduation   $quantityGraduation
     */
    public function __construct(
        SellingUnitId $id,
        ProductDataInterface $product,
        QuantityGraduation $quantityGraduation
    ) {
        $this->id                 = $id;
        $this->product            = $product;
        $this->quantityGraduation = $quantityGraduation;
    }
    
    
    /**
     * @inheritDoc
     */
    public function quantityGraduation(): QuantityGraduation
    {
        return $this->quantityGraduation;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setQuantityGraduation(QuantityGraduation $quantityGraduation): void
    {
        $this->quantityGraduation = $quantityGraduation;
    }
    
    
    /**
     * @inheritDoc
     */
    public function id(): SellingUnitId
    {
        return $this->id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function product(): ProductDataInterface
    {
        return $this->product;
    }
}