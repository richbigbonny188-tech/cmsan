<?php
/* --------------------------------------------------------------
   BaseProduct.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

/**
 * Class BaseProduct
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjectsZ
 */
class BaseProduct
{
    /**
     * @var int
     */
    private int $BaseProductID;
    
    
    /**
     * @var int|null
     */
    private ?int $BaseProductType = null;
    
    
    /**
     * @var array
     */
    private array $BaseProductsRelationData = [];
    
    
    /**
     * @param int $BaseProductID
     */
    public function __construct(int $BaseProductID)
    {
        $this->BaseProductID = $BaseProductID;
    }
    
    
    /**
     * @return int[]
     */
    public function toArray(): array
    {
        $array = [
            'BaseProductID' => $this->BaseProductID,
        ];
        if ($this->BaseProductType !== null) {
            $array['BaseProductType'] = $this->BaseProductType;
        }
        if (!empty($this->BaseProductsRelationData)) {
            $array['BaseProductsRelationData'] = [];
            foreach ($this->BaseProductsRelationData as $baseProductsRelationDatum) {
                $array['BaseProductsRelationData'][] = $baseProductsRelationDatum->toArray();
            }
        }
        
        return $array;
    }
    
    
    /**
     * @return int
     */
    public function getBaseProductID(): int
    {
        return $this->BaseProductID;
    }
    
    
    /**
     * @param int $BaseProductID
     */
    public function setBaseProductID(int $BaseProductID): void
    {
        $this->BaseProductID = $BaseProductID;
    }
    
    
    /**
     * @return int|null
     */
    public function getBaseProductType(): ?int
    {
        return $this->BaseProductType;
    }
    
    
    /**
     * @param int|null $BaseProductType
     */
    public function setBaseProductType(?int $BaseProductType): void
    {
        $this->BaseProductType = $BaseProductType;
    }
    
    
    /**
     * @return array
     */
    public function getBaseProductsRelationData(): array
    {
        return $this->BaseProductsRelationData;
    }
    
    
    /**
     * @param array $BaseProductsRelationData
     */
    public function setBaseProductsRelationData(array $BaseProductsRelationData): void
    {
        $this->BaseProductsRelationData = $BaseProductsRelationData;
    }
    
    
    /**
     * @param BaseProductsRelationData $baseProductsRelationData
     *
     * @return void
     */
    public function addBaseProductsRelationData(BaseProductsRelationData $baseProductsRelationData): void
    {
        $this->BaseProductsRelationData[] = $baseProductsRelationData;
    }
}