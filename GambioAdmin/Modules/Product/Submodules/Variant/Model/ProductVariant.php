<?php
/*--------------------------------------------------------------
   ProductVariant.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsCombination;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsSortOrder;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\UpdatedProductVariantsStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class ProductVariant
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model
 */
class ProductVariant extends AbstractEventRaisingEntity
{
    /**
     * ProductVariant constructor.
     *
     * @param ProductVariantId $id
     * @param ProductId $productId
     * @param OptionAndOptionValueIds $combination
     * @param ImageListId $imageListId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     */
    private function __construct(
        private ProductVariantId             $id,
        private ProductId                    $productId,
        private OptionAndOptionValueIds      $combination,
        private ImageListId                  $imageListId,
        private ProductCustomization         $productCustomization,
        private ProductIdentificationNumbers $productIdentificationNumbers,
        private ProductVariantStock          $stock,
        private int                          $sortOrder = 0
    )
    {
    }

    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId->value();
    }

    /**
     * @return OptionAndOptionValueIds
     */
    public function combination(): OptionAndOptionValueIds
    {
        return $this->combination;
    }

    /**
     * @param OptionAndOptionValueIds $combination
     */
    public function changeCombination(OptionAndOptionValueIds $combination): void
    {
        $this->combination = $combination;
        $this->raiseEvent(UpdatedProductVariantsCombination::create($this->id, $combination));
    }

    /**
     * @param ProductVariantId $variantId
     * @param ProductId $productId
     * @param OptionAndOptionValueIds $combination
     * @param ImageListId $imageListId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     *
     * @return ProductVariant
     */
    public static function create(
        ProductVariantId             $variantId,
        ProductId                    $productId,
        OptionAndOptionValueIds      $combination,
        ImageListId                  $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): ProductVariant
    {
        return new static($variantId,
            $productId,
            $combination,
            $imageListId,
            $productCustomization,
            $productIdentificationNumbers,
            $stock,
            $sortOrder);
    }

    /**
     * @param int $sortOrder
     */
    public function changeSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
        $this->raiseEvent(UpdatedProductVariantsSortOrder::create($this->id, $sortOrder));
    }

    /**
     * @param ProductVariantStock $stock
     */
    public function changeStock(ProductVariantStock $stock): void
    {
        $this->stock = $stock;
        $this->raiseEvent(UpdatedProductVariantsStock::create($this->id, $stock));
    }

    /**
     * @param ImageListId $imageListId
     */
    public function changeImageListId(ImageListId $imageListId): void
    {
        $this->imageListId = $imageListId;
        $this->raiseEvent(UpdatedProductVariantsImageListId::create($this->id, $imageListId));
    }

    /**
     * @param ProductCustomization $productCustomization
     */
    public function changeProductCustomization(ProductCustomization $productCustomization): void
    {
        $this->productCustomization = $productCustomization;
        $this->raiseEvent(UpdatedProductVariantProductCustomization::create($this->id, $productCustomization));
    }

    /**
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     */
    public function changeProductIdentificationNumbers(ProductIdentificationNumbers $productIdentificationNumbers): void
    {
        $this->productIdentificationNumbers = $productIdentificationNumbers;
        $this->raiseEvent(UpdatedProductVariantProductIdentificationNumbers::create($this->id,
            $productIdentificationNumbers));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'combination' => $this->combination->toArray(),
            'sortOrder' => $this->sortOrder(),
            'modelNumber' => $this->modelNumber(),
            'GTIN' => $this->gtin(),
            'ASIN' => $this->asin(),
            'EAN' => $this->ean(),
            'stockType' => $this->stockType(),
            'stock' => $this->stock(),
            'weightType' => $this->weightType(),
            'weight' => $this->weight(),
            'priceType' => $this->priceType(),
            'price' => $this->price(),
            'vpeScalarValue' => $this->vpeScalarValue(),
            'vpeUnitId' => $this->vpeUnitId(),
            'deliveryTimeId' => $this->deliveryTimeId(),
            'imageListId' => $this->imageListId()
        ];
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
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return string
     */
    public function modelNumber(): string
    {
        return $this->productIdentificationNumbers->modelNumber();
    }

    /**
     * @return string
     */
    public function gtin(): string
    {
        return $this->productIdentificationNumbers->gtin();
    }

    /**
     * @return string
     */
    public function asin(): string
    {
        return $this->productIdentificationNumbers->asin();
    }

    /**
     * @return string
     */
    public function ean(): string
    {
        return $this->productIdentificationNumbers->ean();
    }

    /**
     * @return string
     */
    public function stockType(): string
    {
        return $this->stock->stockType();
    }


    /**
     * @return float
     */
    public function stock(): float
    {
        return $this->stock->stock();
    }

    /**
     * @return string
     */
    public function weightType(): string
    {
        return $this->productCustomization->weightType();
    }

    /**
     * @return float
     */
    public function weight(): float
    {
        return $this->productCustomization->weight();
    }

    /**
     * @return string
     */
    public function priceType(): string
    {
        return $this->productCustomization->priceType();
    }

    /**
     * @return float
     */
    public function price(): float
    {
        return $this->productCustomization->price();
    }

    /**
     * @return float
     */
    public function vpeScalarValue(): float
    {
        return $this->productCustomization->vpeScalarValue();
    }

    /**
     * @return int|null
     */
    public function vpeUnitId(): ?int
    {
        return $this->productCustomization->vpeUnitId();
    }

    /**
     * @return int
     */
    public function deliveryTimeId(): int
    {
        return $this->productCustomization->deliveryTimeId();
    }

    /**
     * @return int|null
     */
    public function imageListId(): ?int
    {
        return $this->imageListId->value();
    }
}
