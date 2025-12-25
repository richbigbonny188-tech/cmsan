<?php

/* --------------------------------------------------------------
   QuickEditServiceFactory.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditServiceFactory
 *
 * @category System
 * @package  QuickEdit
 */
class QuickEditServiceFactory implements QuickEditServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * QuickEditServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db Database query builder instance.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates and returns a new quickEdit service instance.
     *
     * @return QuickEditServiceInterface Returns quickEdit service.
     */
    public function createQuickEditService()
    {
        $quickEditProductGraduatedPricesReader     = MainFactory::create('QuickEditProductGraduatedPricesReader',
                                                                         $this->db);
        $quickEditProductGraduatedPricesRepository = MainFactory::create('QuickEditProductGraduatedPricesRepository',
                                                                         $quickEditProductGraduatedPricesReader);
        
        $quickEditPropertiesOverviewColumns   = MainFactory::create('QuickEditPropertiesOverviewColumns');
        $quickEditProductPropertiesReader     = MainFactory::create('QuickEditProductPropertiesReader',
                                                                    $this->db,
                                                                    $quickEditPropertiesOverviewColumns);
        $quickEditProductPropertiesWriter     = MainFactory::create('QuickEditProductPropertiesWriter',
                                                                    $this->db,
                                                                    $quickEditPropertiesOverviewColumns);
        $quickEditProductPropertiesRepository = MainFactory::create('QuickEditProductPropertiesRepository',
                                                                    $quickEditProductPropertiesReader,
                                                                    $quickEditProductPropertiesWriter);
        
        $quickEditSpecialPricesOverviewColumns   = MainFactory::create('QuickEditSpecialPricesOverviewColumns');
        $quickEditProductSpecialPricesReader     = MainFactory::create('QuickEditProductSpecialPricesReader',
                                                                       $this->db,
                                                                       $quickEditSpecialPricesOverviewColumns);
        $quickEditProductSpecialPricesWriter     = MainFactory::create('QuickEditProductSpecialPricesWriter',
                                                                       $this->db,
                                                                       $quickEditSpecialPricesOverviewColumns);
        $quickEditProductSpecialPricesRepository = MainFactory::create('QuickEditProductSpecialPricesRepository',
                                                                       $quickEditProductSpecialPricesReader,
                                                                       $quickEditProductSpecialPricesWriter);
        
        return MainFactory::create('QuickEditService',
                                   $quickEditProductGraduatedPricesRepository,
                                   $quickEditProductPropertiesRepository,
                                   $quickEditProductSpecialPricesRepository);
    }
}