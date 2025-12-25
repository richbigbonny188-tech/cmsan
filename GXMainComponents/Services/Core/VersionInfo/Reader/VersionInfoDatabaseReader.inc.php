<?php

/* --------------------------------------------------------------
   VersionInfoDatabaseReader.inc.php 2022-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoDatabaseReader
 *
 * @category    System
 * @package     VersionInfo
 * @subpackage  Reader
 */
class VersionInfoDatabaseReader extends AbstractVersionInfoReader
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var VersionInfoItemFactory
     */
    protected $versionInfoItemFactory;
    
    
    /**
     * VersionInfoDatabaseReader constructor.
     *
     * @param CI_DB_query_builder    $db
     * @param VersionInfoItemFactory $versionInfoItemFactory
     */
    public function __construct(CI_DB_query_builder $db, VersionInfoItemFactory $versionInfoItemFactory)
    {
        $this->db                     = $db;
        $this->versionInfoItemFactory = $versionInfoItemFactory;
    }
    
    
    /**
     * Returns all VersionInfoDatabaseItems from database.
     *
     * @return VersionInfoDatabaseItem []
     */
    public function getAllVersionInfoItems()
    {
        $versionInfoArray = [];
        
        $resultArray = $this->db->order_by('installation_date', 'ASC')->get('version_history')->result_array();
        
        foreach ($resultArray as $versionInfoData) {
            $versionInfoArray[] = $this->_createVersionInfoObject($versionInfoData);
        }
        
        return $versionInfoArray;
    }
    
    
    /**
     * Returns the last installed version from the database or "null" if no results are found.
     *
     * @return VersionInfoDatabaseItem|null
     */
    public function getLastInstalledVersion()
    {
        $resultArray = $this->db
            ->order_by('history_id', 'DESC')
            ->limit(1)
            ->get('version_history')
            ->row_array();
    
        if (!$resultArray) {
            return null;
        }
        
        return $this->_createVersionInfoObject($resultArray);
    }
    
    
    /**
     * @param array $versionInfoData
     *
     * @return VersionInfoDatabaseItem
     */
    protected function _createVersionInfoObject(array $versionInfoData)
    {
        $name                = new NonEmptyStringType($this->_determineName(new NonEmptyStringType($versionInfoData['name'])));
        $version             = new NonEmptyStringType(strtolower($versionInfoData['version']));
        $vendor              = new NonEmptyStringType('Gambio');
        $changelogVersionURL = new NonEmptyStringType($this->_determineChangelogURL($version, $vendor, $name));
        $installationDate    = new DateTime($versionInfoData['installation_date']);
        $installedStatus     = new BoolType($versionInfoData['installed']);
        
        return $this->versionInfoItemFactory->createVersionInfoDatabaseItem($name,
                                                                            $version,
                                                                            $vendor,
                                                                            $changelogVersionURL,
                                                                            $installationDate,
                                                                            $installedStatus);
    }
}
