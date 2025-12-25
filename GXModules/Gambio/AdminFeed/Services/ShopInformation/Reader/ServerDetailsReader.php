<?php
/* --------------------------------------------------------------
   ServerDetailsReader.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Reader;

use CI_DB_query_builder;

/**
 * Class ServerDetailsReader
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Reader
 */
class ServerDetailsReader
{
    /**
     * @var CI_DB_query_builder
     */
    private $db;
    
    
    /**
     * ServerDetailsReader constructor.
     *
     * @param CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns the php version.
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }
    
    
    /**
     * Returns a list of available php extensions.
     *
     * @return array
     */
    public function getPhpExtensions()
    {
        return get_loaded_extensions();
    }
    
    
    /**
     * Returns a list of the used php configuration.
     *
     * @return array
     */
    public function getPhpConfiguration()
    {
        return ini_get_all();
    }
    
    
    /**
     * Returns the mysql version.
     *
     * @return string
     */
    public function getMysqlVersion()
    {
        return $this->db->version();
    }
    
    
    /**
     * Returns a list of available mysql engines.
     *
     * @return array
     */
    public function getMysqlEngines()
    {
        $return = [];
        
        $engines = $this->db->query('SHOW ENGINES;')->result_array();
        foreach ($engines as $engine) {
            $return[] = $engine['Engine'];
        }
        
        return $return;
    }
    
    
    /**
     * Returns the default engine of mysql.
     *
     * @return string
     */
    public function getMysqlDefaultEngine()
    {
        $return = '';
        
        $engines = $this->db->query('SHOW ENGINES;')->result_array();
        foreach ($engines as $engine) {
            if ($engine['Support'] === 'DEFAULT') {
                $return = $engine['Engine'];
                break;
            }
        }
        
        return $return;
    }
    
    
    /**
     * Returns the software name that is used for the web server.
     *
     * @return string
     */
    public function getWebserver()
    {
        return isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
    }
    
    
    /**
     * Returns the name of the operating system.
     *
     * @return string
     */
    public function getOperatingSystem()
    {
        return defined('PHP_OS') ? PHP_OS : '';
    }
}