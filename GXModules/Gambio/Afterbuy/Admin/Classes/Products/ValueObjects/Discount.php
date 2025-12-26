<?php
/* --------------------------------------------------------------
   Discount.php 2023-10-18
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
 * Class Discount
 * 
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class Discount
{
    /**
     * @var int
     */
    private int $ShopID;
    
    /**
     * @var bool
     */
    private bool $DiscountActive;
    
    /**
     * @var int
     */
    private int $ControlId;
    
    /**
     * @var string
     */
    private string $PriceType;
    
    /**
     * @var string
     */
    private string $NewPriceType;
    
    /**
     * @var \DateTimeImmutable|null
     */
    private \DateTimeImmutable $StartDate;
    
    /**
     * @var \DateTimeImmutable|null
     */
    private \DateTimeImmutable $ExpireDate;
    
    /**
     * @var int
     */
    private int $Type;
    
    /**
     * @var float|int
     */
    private float $DiscountPercent;
    
    /**
     * @var float|int
     */
    private float $DiscountAmount;
    
    /**
     * @var float|int
     */
    private float $SavedAmount;
    
    /**
     * @var float|int
     */
    private float $DiscountedPrice;
    /**
     * @var int|null
     */
    private ?int $Quantity;
    
    
    /**
     * @param int                     $ShopID
     * @param bool                    $DiscountActive
     * @param int                     $ControlId
     * @param string                  $PriceType
     * @param string                  $NewPriceType
     * @param \DateTimeImmutable|null $StartDate
     * @param \DateTimeImmutable|null $ExpireDate
     * @param int                     $Type
     * @param float                   $DiscountPercent
     * @param float                   $DiscountAmount
     * @param float                   $SavedAmount
     * @param float                   $DiscountedPrice
     * @param int|null                $Quantity
     */
    public function __construct(
        int                 $ShopID = 0,
        bool                $DiscountActive = false,
        int                 $ControlId = 3,
        string              $PriceType = '',
        string              $NewPriceType = '',
        ?\DateTimeImmutable $StartDate = null,
        ?\DateTimeImmutable $ExpireDate = null,
        int                 $Type = 0,
        float               $DiscountPercent = 0,
        float               $DiscountAmount = 0,
        float               $SavedAmount = 0,
        float               $DiscountedPrice = 0,
        ?int                $Quantity = null
    ) {
        $this->ShopID          = $ShopID;
        $this->DiscountActive  = $DiscountActive;
        $this->ControlId       = $ControlId;
        $this->PriceType       = $PriceType;
        $this->NewPriceType    = $NewPriceType;
        $this->StartDate       = $StartDate;
        $this->ExpireDate      = $ExpireDate;
        $this->Type            = $Type;
        $this->DiscountPercent = $DiscountPercent;
        $this->DiscountAmount  = $DiscountAmount;
        $this->SavedAmount     = $SavedAmount;
        $this->DiscountedPrice = $DiscountedPrice;
        $this->Quantity        = $Quantity;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'ShopId'          => $this->ShopID,
            'DiscountActive'  => $this->DiscountActive ? 'true' : 'false',
            'ControlID'       => $this->ControlId,
            'PriceType'       => $this->PriceType,
            'NewPriceType'    => $this->NewPriceType,
            'StartDate'       => $this->StartDate->format('c'),
            'ExpireDate'      => $this->ExpireDate->format('c'),
            'Type'            => $this->Type,
            'DiscountPercent' => $this->DiscountPercent,
            'DiscountAmount'  => $this->DiscountAmount,
            'SavedAmount'     => $this->SavedAmount,
            'DiscountedPrice' => $this->DiscountedPrice,
            'Quantity'        => (string)($this->Quantity ?? 'none'),
        ];
    }
    
    
    /**
     * @return int
     */
    public function getShopID(): int
    {
        return $this->ShopID;
    }
    
    
    /**
     * @param int $ShopID
     */
    public function setShopID(int $ShopID): void
    {
        $this->ShopID = $ShopID;
    }
    
    
    /**
     * @return bool
     */
    public function isDiscountActive(): bool
    {
        return $this->DiscountActive;
    }
    
    
    /**
     * @param bool $DiscountActive
     */
    public function setDiscountActive(bool $DiscountActive): void
    {
        $this->DiscountActive = $DiscountActive;
    }
    
    
    /**
     * @return int
     */
    public function getControlId(): int
    {
        return $this->ControlId;
    }
    
    
    /**
     * @param int $ControlId
     */
    public function setControlId(int $ControlId): void
    {
        $this->ControlId = $ControlId;
    }
    
    
    /**
     * @return string
     */
    public function getPriceType(): string
    {
        return $this->PriceType;
    }
    
    
    /**
     * @param string $PriceType
     */
    public function setPriceType(string $PriceType): void
    {
        $this->PriceType = $PriceType;
    }
    
    
    /**
     * @return string
     */
    public function getNewPriceType(): string
    {
        return $this->NewPriceType;
    }
    
    
    /**
     * @param string $NewPriceType
     */
    public function setNewPriceType(string $NewPriceType): void
    {
        $this->NewPriceType = $NewPriceType;
    }
    
    
    /**
     * @return \DateTimeImmutable
     */
    public function getStartDate(): \DateTimeImmutable
    {
        return $this->StartDate;
    }
    
    
    /**
     * @param \DateTimeImmutable $StartDate
     */
    public function setStartDate(\DateTimeImmutable $StartDate): void
    {
        $this->StartDate = $StartDate;
    }
    
    
    /**
     * @return \DateTimeImmutable
     */
    public function getExpireDate(): \DateTimeImmutable
    {
        return $this->ExpireDate;
    }
    
    
    /**
     * @param \DateTimeImmutable $ExpireDate
     */
    public function setExpireDate(\DateTimeImmutable $ExpireDate): void
    {
        $this->ExpireDate = $ExpireDate;
    }
    
    
    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->Type;
    }
    
    
    /**
     * @param int $Type
     */
    public function setType(int $Type): void
    {
        $this->Type = $Type;
    }
    
    
    /**
     * @return float
     */
    public function getDiscountPercent(): float
    {
        return $this->DiscountPercent;
    }
    
    
    /**
     * @param float $DiscountPercent
     */
    public function setDiscountPercent(float $DiscountPercent): void
    {
        $this->DiscountPercent = $DiscountPercent;
    }
    
    
    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->DiscountAmount;
    }
    
    
    /**
     * @param float $DiscountAmount
     */
    public function setDiscountAmount(float $DiscountAmount): void
    {
        $this->DiscountAmount = $DiscountAmount;
    }
    
    
    /**
     * @return float
     */
    public function getSavedAmount(): float
    {
        return $this->SavedAmount;
    }
    
    
    /**
     * @param float $SavedAmount
     */
    public function setSavedAmount(float $SavedAmount): void
    {
        $this->SavedAmount = $SavedAmount;
    }
    
    
    /**
     * @return float
     */
    public function getDiscountedPrice(): float
    {
        return $this->DiscountedPrice;
    }
    
    
    /**
     * @param float $DiscountedPrice
     */
    public function setDiscountedPrice(float $DiscountedPrice): void
    {
        $this->DiscountedPrice = $DiscountedPrice;
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
    
    
}