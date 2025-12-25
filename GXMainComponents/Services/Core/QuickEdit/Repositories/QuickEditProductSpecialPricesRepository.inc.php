<?php

/* --------------------------------------------------------------
   QuickEditProductSpecialPricesRepository.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductSpecialPricesRepository
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductSpecialPricesRepository implements QuickEditProductSpecialPricesRepositoryInterface
{
    /**
     * @var IntType
     */
    protected $length;
    
    /**
     * @var IntType
     */
    protected $start;
    
    /**
     * @var StringType
     */
    protected $orderBy;
    
    /**
     * @var QuickEditProductSpecialPricesReaderInterface
     */
    protected $specialPriceReader;
    
    /**
     * @var QuickEditProductSpecialPricesWriterInterface
     */
    protected $specialPriceWriter;
    
    
    /**
     * QuickEditProductSpecialPricesRepository constructor.
     *
     * @param QuickEditProductSpecialPricesReaderInterface $specialPricesReader Special prices reader.
     * @param QuickEditProductSpecialPricesWriterInterface $specialPricesWriter Special prices writer.
     */
    public function __construct(
        QuickEditProductSpecialPricesReaderInterface $specialPricesReader,
        QuickEditProductSpecialPricesWriterInterface $specialPricesWriter
    ) {
        $this->specialPriceReader = $specialPricesReader;
        $this->specialPriceWriter = $specialPricesWriter;
    }
    
    
    /**
     * Returns the special prices of the indicated products.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredSpecialPrices(array $productIds, array $filterParameters)
    {
        $specialPrices = $this->specialPriceReader->paginateSpecialPrices($this->start, $this->length)
            ->sortSpecialPrices($this->orderBy)
            ->getFilteredSpecialPrices($productIds, $filterParameters);
        
        return $this->_collectionContentArray($specialPrices);
    }
    
    
    /**
     * Returns the record number of the filtered special prices.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return int Returns the number of special prices found.
     */
    public function getFilteredSpecialPricesCount(array $productIds, array $filterParameters)
    {
        return $this->specialPriceReader->getFilteredSpecialPricesCount($productIds, $filterParameters);
    }
    
    
    /**
     * Get special prices record count.
     *
     * @return int Returns the number of all special prices found.
     */
    public function getSpecialPricesCount()
    {
        return $this->specialPriceReader->getSpecialPricesCount();
    }
    
    
    /**
     * Saves the changed data regarding the special price.
     *
     * @param array $specialPrice Contains the special prices data.
     *
     * @return bool Returns the operation result.
     */
    public function setSpecialPriceById(array $specialPrice)
    {
        return $this->specialPriceWriter->setSpecialPriceById($specialPrice);
    }
    
    
    /**
     * Sets the starting point of the pagination and the number of special prices.
     *
     * @param IntType|null $start  Pagination start index.
     * @param IntType|null $length Page length value.
     *
     * @return QuickEditProductSpecialPricesRepository QuickEdit products special price repository for chained method
     *                                                 calls.
     */
    public function paginateSpecialPrices(IntType $start = null, IntType $length = null)
    {
        $this->start  = $start;
        $this->length = $length;
        
        return $this;
    }
    
    
    /**
     * Sets the sorting order of the special prices.
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC)
     *
     * @return QuickEditProductSpecialPricesRepository QuickEdit products special price repository for chained method
     *                                                 calls.
     */
    public function sortSpecialPrices(StringType $orderBy = null)
    {
        $this->orderBy = $orderBy;
        
        return $this;
    }
    
    
    /**
     * Returns an array of QuickEditProductSpecialPriceListItem.
     *
     * @param array $specialPrices An array containing the data required for a product.
     *
     * @return array Returns an array of QuickEditProductSpecialPriceListItem or an empty array.
     */
    protected function _collectionContentArray(array $specialPrices)
    {
        $collection = [];
        foreach ($specialPrices as $value) {
            $collection[] = MainFactory::create('QuickEditProductSpecialPriceListItem', $value);
        }
        
        return $collection;
    }
}