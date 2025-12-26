<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesRepository.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductPropertiesRepository
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Repositories
 */
class QuickEditProductPropertiesRepository implements QuickEditProductPropertiesRepositoryInterface
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
     * @var QuickEditProductPropertiesReaderInterface
     */
    protected $reader;
    
    /**
     * @var QuickEditProductPropertiesWriterInterface
     */
    protected $writer;
    
    
    /**
     * QuickEditProductPropertiesRepository constructor.
     *
     * @param QuickEditProductPropertiesReaderInterface|null $reader Products properties reader.
     * @param QuickEditProductPropertiesWriterInterface|null $writer Products properties writer.
     */
    public function __construct(
        QuickEditProductPropertiesReaderInterface $reader = null,
        QuickEditProductPropertiesWriterInterface $writer = null
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
    }
    
    
    /**
     * Returns product properties based on the provided filter criteria.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredProductProperties(array $productIds, array $filterParameters)
    {
        $properties = $this->reader->paginateProperties($this->start, $this->length)
            ->sortProperties($this->orderBy)
            ->getFilteredProductProperties($productIds, $filterParameters);
        
        return $this->_collectionContentArray($properties);
    }
    
    
    /**
     * Returns products that are subject to the specified filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return int Returns the number of product properties found.
     */
    public function getFilteredProductPropertiesCount(array $productIds, array $filterParameters)
    {
        return $this->reader->getFilteredProductPropertiesCount($productIds, $filterParameters);
    }
    
    
    /**
     * Returns the number of all product properties found.
     *
     * @return int Returns the record number.
     */
    public function getProductPropertiesCount()
    {
        return $this->reader->getProductPropertiesCount();
    }
    
    
    /**
     * Saves product by product-combi ID.
     *
     * @param array $productCombi Contains product data to be saved.
     *
     * @return bool Returns the operation result.
     */
    public function setByCombisId(array $productCombi)
    {
        return $this->writer->setByCombisId($productCombi);
    }
    
    
    /**
     * Sets the starting point of the pagination and the number of products.
     *
     * @param IntType|null $start  Pagination start index.
     * @param IntType|null $length Page length value.
     *
     * @return QuickEditProductPropertiesRepository QuickEdit products properties repository for chained method calls.
     */
    public function paginateProperties(IntType $start = null, IntType $length = null)
    {
        $this->start  = $start;
        $this->length = $length;
        
        return $this;
    }
    
    
    /**
     * Sets the sorting order of the product properties.
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC).
     *
     * @return QuickEditProductPropertiesRepository QuickEdit products properties repository for chained method calls.
     */
    public function sortProperties(StringType $orderBy = null)
    {
        $this->orderBy = $orderBy;
        
        return $this;
    }
    
    
    /**
     * Returns an array of QuickEditProductPropertiesListItem.
     *
     * @param array $properties Array containing property information.
     *
     * @return array Returns an array of QuickEditProductPropertiesListItem or an empty array.
     */
    protected function _collectionContentArray(array $properties)
    {
        $collection = [];
        
        foreach ($properties as $value) {
            $collection[] = MainFactory::create('QuickEditProductPropertiesListItem', $value);
        }
        
        return $collection;
    }
}