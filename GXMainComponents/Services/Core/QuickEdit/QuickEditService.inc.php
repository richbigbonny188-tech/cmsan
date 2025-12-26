<?php

/* --------------------------------------------------------------
   QuickEditService.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditService
 *
 * @category System
 * @package  QuickEdit
 */
class QuickEditService implements QuickEditServiceInterface
{
    /**
     * @var QuickEditProductGraduatedPricesRepositoryInterface
     */
    protected $graduatedPricesRepository;
    
    /**
     * @var QuickEditProductPropertiesRepositoryInterface
     */
    protected $propertiesRepository;
    
    /**
     * @var QuickEditProductSpecialPricesRepositoryInterface
     */
    protected $specialPricesRepository;
    
    
    /**
     * QuickEditService constructor.
     *
     * @param QuickEditProductGraduatedPricesRepositoryInterface $graduatedPricesRepository Graduations repository
     *                                                                                      instance.
     * @param QuickEditProductPropertiesRepositoryInterface      $propertiesRepository      Properties repository
     *                                                                                      instance.
     * @param QuickEditProductSpecialPricesRepositoryInterface   $specialPricesRepository   Special price repository
     *                                                                                      instance.
     */
    public function __construct(
        QuickEditProductGraduatedPricesRepositoryInterface $graduatedPricesRepository,
        QuickEditProductPropertiesRepositoryInterface $propertiesRepository,
        QuickEditProductSpecialPricesRepositoryInterface $specialPricesRepository
    ) {
        $this->graduatedPricesRepository = $graduatedPricesRepository;
        $this->propertiesRepository      = $propertiesRepository;
        $this->specialPricesRepository   = $specialPricesRepository;
    }
    
    
    /**
     * Returns the graduated prices of a product or an empty array nothing was found.
     *
     * @param array|null $productIds Array containing the selected product IDs to be processed.
     *
     * @return array Returns array that contains the graduated prices information.
     */
    public function getGraduatedPrices(array $productIds = null)
    {
        return $this->graduatedPricesRepository->getGraduatedPrices($productIds);
    }
    
    
    /**
     * Returns filtered product properties based on the provided filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredProductProperties(array $productIds, array $filterParameters)
    {
        return $this->propertiesRepository->getFilteredProductProperties($productIds, $filterParameters);
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
        return $this->propertiesRepository->getFilteredProductPropertiesCount($productIds, $filterParameters);
    }
    
    
    /**
     * Returns the number of all product properties found.
     *
     * @return int Returns the record number.
     */
    public function getProductPropertiesCount()
    {
        return $this->propertiesRepository->getProductPropertiesCount();
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
        return $this->propertiesRepository->paginateProperties($start, $length);
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
        return $this->propertiesRepository->sortProperties($orderBy);
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
        return $this->propertiesRepository->setByCombisId($productCombi);
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
        return $this->specialPricesRepository->getFilteredSpecialPrices($productIds, $filterParameters);
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
        return $this->specialPricesRepository->getFilteredSpecialPricesCount($productIds, $filterParameters);
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
        return $this->specialPricesRepository->paginateSpecialPrices($start, $length);
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
        return $this->specialPricesRepository->sortSpecialPrices($orderBy);
    }
    
    
    /**
     * Get special prices record count.
     *
     * @return int Returns the number of all special prices found.
     */
    public function getSpecialPricesCount()
    {
        return $this->specialPricesRepository->getSpecialPricesCount();
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
        return $this->specialPricesRepository->setSpecialPriceById($specialPrice);
    }
}