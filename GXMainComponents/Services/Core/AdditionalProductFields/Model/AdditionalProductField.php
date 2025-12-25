<?php
/*--------------------------------------------------------------
   AdditionalProductField.php 2021-08-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\AdditionalProductFields\Model;

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\Collections\AdditionalFieldValues;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldId;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\AdditionalFieldValue;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\Model\ValueObjects\ProductId;
use MainFactory;

/**
 * Class AdditionalProductField
 * @package Gambio\MainComponents\Services\Core\AdditionalProductFields\Model
 */
class AdditionalProductField
{
    /**
     * @var AdditionalFieldId
     */
    protected $id;
    
    /**
     * @var ProductId
     */
    protected $productId;
    
    /**
     * @var AdditionalFieldValues
     */
    protected $values;
    
    
    /**
     * AdditionalProductField constructor.
     *
     * @param AdditionalFieldId     $id
     * @param ProductId             $productId
     * @param AdditionalFieldValues $values
     */
    public function __construct(
        AdditionalFieldId $id,
        ProductId $productId,
        AdditionalFieldValues $values
    ) {
        $this->id        = $id;
        $this->productId = $productId;
        $this->values    = $values;
    }
    
    
    /**
     * @param AdditionalFieldId     $id
     * @param ProductId             $productId
     * @param AdditionalFieldValues $values
     *
     * @return AdditionalProductField
     */
    public static function create(
        AdditionalFieldId $id,
        ProductId $productId,
        AdditionalFieldValues $values
    ): AdditionalProductField{
        
        return MainFactory::create(AdditionalProductField::class, $id, $productId, $values);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId->value();
    }
    
    
    /**
     * @return AdditionalFieldValues
     */
    public function values(): AdditionalFieldValues
    {
        return $this->values;
    }
    
    
    /**
     * @param AdditionalFieldValue ...$values
     */
    public function changeValues(AdditionalFieldValue ...$values): void
    {
        $this->values = $this->values->with(...$values);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'     => $this->id(),
            'values' => $this->values()->toArray(),
        ];
    }
}