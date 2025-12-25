<?php

/* --------------------------------------------------------------
   InfoBoxFactory.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxFactory
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Factories
 */
class InfoBoxFactory implements InfoBoxFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * Class constructor
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates an InfoBoxService instance.
     *
     * @return InfoBoxService
     */
    public function createInfoBoxService()
    {
        $repository = $this->_createInfoBoxRepository();
        
        return MainFactory::create('InfoBoxService', $repository);
    }
    
    
    /**
     * Create an InfoBoxRepository instance.
     *
     * @return InfoBoxRepository
     */
    protected function _createInfoBoxRepository()
    {
        $reader  = $this->_createInfoBoxRepositoryReader();
        $writer  = $this->_createInfoBoxRepositoryWriter();
        $deleter = $this->_createInfoBoxRepositoryDeleter();
        
        return MainFactory::create('InfoBoxRepository', $reader, $writer, $deleter);
    }
    
    
    /**
     * Create an InfoBoxRepositoryReader instance.
     *
     * @return InfoBoxRepositoryReader
     */
    protected function _createInfoBoxRepositoryReader()
    {
        return MainFactory::create('InfoBoxRepositoryReader', $this->db);
    }
    
    
    /**
     * Create an InfoBoxRepositoryWriter instance.
     *
     * @return InfoBoxRepositoryWriter
     */
    protected function _createInfoBoxRepositoryWriter()
    {
        return MainFactory::create('InfoBoxRepositoryWriter', $this->db);
    }
    
    
    /**
     * Create an InfoBoxRepositoryDeleter instance.
     *
     * @return InfoBoxRepositoryDeleter
     */
    protected function _createInfoBoxRepositoryDeleter()
    {
        return MainFactory::create('InfoBoxRepositoryDeleter', $this->db);
    }
}