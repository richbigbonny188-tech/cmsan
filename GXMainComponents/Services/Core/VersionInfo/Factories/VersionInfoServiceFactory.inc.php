<?php
/* --------------------------------------------------------------
   VersionInfoServiceFactory.inc.php 2017-03-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoServiceFactory
 *
 * @category   Core
 * @package    VersionInfo
 * @subpackage Factories
 */
class VersionInfoServiceFactory
{
    /**
     * Database connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Absolute file path for the version info receipts.
     *
     * @var ExistingDirectory
     */
    protected $versionInfoFilePath;
    
    /**
     * @var VersionInfoItemFactory
     */
    protected $versionInfoItemFactory;
    
    
    /**
     * VersionInfoServiceFactory constructor.
     *
     * @param CI_DB_query_builder $db                  Database connection.
     * @param ExistingDirectory   $versionInfoFilePath Existing Directory.
     */
    public function __construct(CI_DB_query_builder $db, ExistingDirectory $versionInfoFilePath)
    {
        $this->db                  = $db;
        $this->versionInfoFilePath = $versionInfoFilePath;
    }
    
    
    /**
     * Creates a version info read service.
     *
     * @return VersionInfoService
     */
    public function createVersionInfoService()
    {
        $repository = $this->_createVersionInfoRepository();
        
        return MainFactory::create('VersionInfoService', $repository);
    }
    
    
    /**
     * @return VersionInfoRepository
     */
    protected function _createVersionInfoRepository()
    {
        $databaseReader = $this->_createVersionInfoDatabaseReader();
        $fileReader     = $this->_createVersionInfoFileReader();
        
        return MainFactory::create('VersionInfoRepository', $databaseReader, $fileReader);
    }
    
    
    /**
     * @return VersionInfoFileReader
     */
    protected function _createVersionInfoFileReader()
    {
        return MainFactory::create('VersionInfoFileReader',
                                   $this->versionInfoFilePath,
                                   $this->_createVersionInfoItemFactory());
    }
    
    
    /**
     * @return VersionInfoDatabaseReader
     */
    protected function _createVersionInfoDatabaseReader()
    {
        return MainFactory::create('VersionInfoDatabaseReader', $this->db, $this->_createVersionInfoItemFactory());
    }
    
    
    protected function _createVersionInfoItemFactory()
    {
        if ($this->versionInfoItemFactory === null) {
            $this->versionInfoItemFactory = MainFactory::create('VersionInfoItemFactory');
        }
        
        return $this->versionInfoItemFactory;
    }
}