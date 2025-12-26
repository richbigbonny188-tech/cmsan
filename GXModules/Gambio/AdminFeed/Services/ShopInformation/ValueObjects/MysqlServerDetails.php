<?php
/* --------------------------------------------------------------
   MysqlServerDetails.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\ValueObjects;

/**
 * Class MysqlServerDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class MysqlServerDetails
{
    /**
     * @var string
     */
    private $version;
    
    /**
     * @var array
     */
    private $engines;
    
    /**
     * @var string
     */
    private $defaultEngine;
    
    
    /**
     * MysqlServerDetails constructor.
     *
     * @param string $version
     * @param array  $engines
     * @param string $defaultEngine
     */
    public function __construct($version, array $engines, $defaultEngine)
    {
        $this->version       = $version;
        $this->engines       = $engines;
        $this->defaultEngine = $defaultEngine;
    }
    
    
    /**
     * Creates and returns a new MysqlServerDetails instance.
     *
     * @param string $version
     * @param array  $engines
     * @param string $defaultEngine
     *
     * @return MysqlServerDetails
     */
    static function create($version, array $engines, $defaultEngine)
    {
        return new self($version, $engines, $defaultEngine);
    }
    
    
    /**
     * Returns the mysql version.
     *
     * @return string
     */
    public function version()
    {
        return $this->version;
    }
    
    
    /**
     * Returns the available mysql engines.
     *
     * @return array
     */
    public function engines()
    {
        return $this->engines;
    }
    
    
    /**
     * Returns the default engine that is used by mysql.
     *
     * @return string
     */
    public function defaultEngine()
    {
        return $this->defaultEngine;
    }
}