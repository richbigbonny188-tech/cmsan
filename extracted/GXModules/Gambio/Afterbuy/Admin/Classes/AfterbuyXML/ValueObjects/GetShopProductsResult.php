<?php
/* --------------------------------------------------------------
   GetShopProductsResult.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\ValueObjects;

/**
 * Class GetShopProductsResult
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\AfterbuyXML\ValueObjects
 */
class GetShopProductsResult
{
    /**
     * @var array
     */
    private array $products;
    
    
    /**
     * @var bool
     */
    private bool $hasMoreProducts;
    
    
    /**
     * @var int
     */
    private int $lastProductId;
    
    
    /**
     * @var int|null
     */
    private ?int $totalNumberOfEntries;
    
    
    /**
     * @var int|null
     */
    private ?int $totalNumberOfPages;
    
    
    /**
     * @var int|null
     */
    private ?int $itemsPerPage;
    
    
    /**
     * @var int|null
     */
    private ?int $pageNumber;
    
    
    /**
     * @param array    $products
     * @param bool     $hasMoreProducts
     * @param int      $lastProductId
     * @param int|null $totalNumberOfEntries
     * @param int|null $totalNumberOfPages
     * @param int|null $itemsPerPage
     * @param int|null $pageNumber
     */
    public function __construct(
        array $products,
        bool  $hasMoreProducts,
        int   $lastProductId,
        ?int  $totalNumberOfEntries = null,
        ?int  $totalNumberOfPages = null,
        ?int  $itemsPerPage = null,
        ?int  $pageNumber = null
    ) {
        $this->products             = $products;
        $this->hasMoreProducts      = $hasMoreProducts;
        $this->lastProductId        = $lastProductId;
        $this->totalNumberOfEntries = $totalNumberOfEntries;
        $this->totalNumberOfPages   = $totalNumberOfPages;
        $this->itemsPerPage         = $itemsPerPage;
        $this->pageNumber           = $pageNumber;
    }
    
    
    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }
    
    
    /**
     * @param array $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
    
    
    /**
     * @return bool
     */
    public function isHasMoreProducts(): bool
    {
        return $this->hasMoreProducts;
    }
    
    
    /**
     * @param bool $hasMoreProducts
     */
    public function setHasMoreProducts(bool $hasMoreProducts): void
    {
        $this->hasMoreProducts = $hasMoreProducts;
    }
    
    
    /**
     * @return int
     */
    public function getLastProductId(): int
    {
        return $this->lastProductId;
    }
    
    
    /**
     * @param int $lastProductId
     */
    public function setLastProductId(int $lastProductId): void
    {
        $this->lastProductId = $lastProductId;
    }
    
    
    /**
     * @return int|null
     */
    public function getTotalNumberOfEntries(): ?int
    {
        return $this->totalNumberOfEntries;
    }
    
    
    /**
     * @param int|null $totalNumberOfEntries
     */
    public function setTotalNumberOfEntries(?int $totalNumberOfEntries): void
    {
        $this->totalNumberOfEntries = $totalNumberOfEntries;
    }
    
    
    /**
     * @return int|null
     */
    public function getTotalNumberOfPages(): ?int
    {
        return $this->totalNumberOfPages;
    }
    
    
    /**
     * @param int|null $totalNumberOfPages
     */
    public function setTotalNumberOfPages(?int $totalNumberOfPages): void
    {
        $this->totalNumberOfPages = $totalNumberOfPages;
    }
    
    
    /**
     * @return int|null
     */
    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }
    
    
    /**
     * @param int|null $itemsPerPage
     */
    public function setItemsPerPage(?int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
    
    
    /**
     * @return int|null
     */
    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }
    
    
    /**
     * @param int|null $pageNumber
     */
    public function setPageNumber(?int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }
    
    
}