<?php
/* --------------------------------------------------------------
   UpdateDetails.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\ValueObjects;

use InvalidArgumentException;

/**
 * Class UpdateDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class UpdateDetails
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $version;
    
    /**
     * @var string
     */
    private $installationDate;
    
    
    /**
     * UpdateDetails constructor.
     *
     * @param $name
     * @param $version
     * @param $installationDate
     */
    public function __construct($name, $version, $installationDate)
    {
        $this->name             = $name;
        $this->version          = $version;
        $this->installationDate = $installationDate;
    }
    
    
    /**
     * Creates and returns a new UpdateDetails instance.
     *
     * @param string $name
     * @param string $version
     * @param string $installationDate
     *
     * @return UpdateDetails
     */
    static function create($name, $version, $installationDate)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name can not be empty.');
        } elseif (empty($version)) {
            throw new InvalidArgumentException('Version can not be empty.');
        }
        
        return new self($name, $version, $installationDate);
    }
    
    
    /**
     * Returns the name of the update.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the version of the update.
     *
     * @return string
     */
    public function version()
    {
        return $this->version;
    }
    
    
    /**
     * Returns the installation date of the update.
     *
     * @return string
     */
    public function installationDate()
    {
        return $this->installationDate;
    }
}