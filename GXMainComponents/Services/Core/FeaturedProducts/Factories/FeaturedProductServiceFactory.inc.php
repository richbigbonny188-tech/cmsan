<?php
/* --------------------------------------------------------------
   FeaturedProductServiceFactory.inc.php 2019-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FeaturedProductServiceFactory
 */
class FeaturedProductServiceFactory implements FeaturedProductServiceFactoryInterface
{
    /**
     * CodeIgniter QueryBuilder
     *
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * Featured product read service.
     *
     * @var FeaturedProductReadService
     */
    protected $readService;
    
    /**
     * @var FeaturedProductRepository
     */
    protected $repository;
    
    /**
     * @var FeaturedProductReader
     */
    protected $reader;
    
    /**
     * @var BoolType
     */
    protected $isCustomerGroupCheckActive;
    
    
    /**
     * FeaturedProductServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder, BoolType $isCustomerGroupCheckActive)
    {
        $this->queryBuilder               = $queryBuilder;
        $this->isCustomerGroupCheckActive = $isCustomerGroupCheckActive;
    }
    
    
    /**
     * Returns the read service.
     *
     * @return FeaturedProductReadService
     */
    public function createReadService()
    {
        if ($this->readService === null) {
            $this->readService = MainFactory::create(FeaturedProductReadService::class, $this->repository());
        }
        
        return $this->readService;
    }
    
    
    /**
     * return a featured product repository.
     *
     * @return FeaturedProductRepository
     */
    protected function repository()
    {
        if ($this->repository === null) {
            $this->repository = MainFactory::create(FeaturedProductRepository::class, $this->reader());
        }
        
        return $this->repository;
    }
    
    
    /**
     * Return a featured product reader.
     *
     * @return FeaturedProductReader
     */
    protected function Reader()
    {
        if ($this->reader === null) {
            $this->reader = MainFactory::create(FeaturedProductReader::class,
                                                $this->queryBuilder,
                                                $this->isCustomerGroupCheckActive);
        }
        
        return $this->reader;
    }
}