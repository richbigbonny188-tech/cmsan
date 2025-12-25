<?php
/* --------------------------------------------------------------
   BaseProductsRelationData.php 2023-10-18
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
 * Class BaseProductsRelationData
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class BaseProductsRelationData
{
    /**
     * @var int|null
     */
    private ?int $Quantity = null;
    
    /**
     * @var string|null
     */
    private ?string $VariationLabel = null;
    
    /**
     * @var string|null
     */
    private ?string $DefaultProduct = null;
    
    /**
     * @var string
     */
    private string $Position = '0';
    
    /**
     * @var array
     */
    private array $eBayVariationData = [];
    
    
    /**
     *
     */
    public function __construct()
    {
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        if ($this->Quantity !== null) {
            $array['Quantity'] = $this->Quantity;
        }
        if ($this->VariationLabel !== null) {
            $array['VariationLabel'] = $this->VariationLabel;
        }
        if ($this->DefaultProduct !== null) {
            $array['DefaultProduct'] = $this->DefaultProduct;
        }
        $array['Position'] = $this->Position;
        if (!empty($this->eBayVariationData)) {
            $array['eBayVariationData'] = [];
            foreach ($this->eBayVariationData as $eBayVariationDatum) {
                $array['eBayVariationData'][] = $eBayVariationDatum->toArray();
            }
        }
        
        return $array;
    }
    
    
    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }
    
    
    /**
     * @param int|null $Quantity
     */
    public function setQuantity(?int $Quantity): void
    {
        $this->Quantity = $Quantity;
    }
    
    
    /**
     * @return string|null
     */
    public function getVariationLabel(): ?string
    {
        return $this->VariationLabel;
    }
    
    
    /**
     * @param string|null $VariationLabel
     */
    public function setVariationLabel(?string $VariationLabel): void
    {
        $this->VariationLabel = $VariationLabel;
    }
    
    
    /**
     * @return string|null
     */
    public function getDefaultProduct(): ?string
    {
        return $this->DefaultProduct;
    }
    
    
    /**
     * @param string|null $DefaultProduct
     */
    public function setDefaultProduct(?string $DefaultProduct): void
    {
        $this->DefaultProduct = $DefaultProduct;
    }
    
    
    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->Position;
    }
    
    
    /**
     * @param string $Position
     */
    public function setPosition(string $Position): void
    {
        $this->Position = $Position;
    }
    
    
    /**
     * @return array
     */
    public function getEBayVariationData(): array
    {
        return $this->eBayVariationData;
    }
    
    
    /**
     * @param array $eBayVariationData
     */
    public function setEBayVariationData(array $eBayVariationData): void
    {
        $this->eBayVariationData = $eBayVariationData;
    }
    
    
    /**
     * @param EBayVariationData $EBayVariationData
     *
     * @return void
     */
    public function addEBayVariationData(EBayVariationData $EBayVariationData): void
    {
        $this->eBayVariationData[] = $EBayVariationData;
    }
}