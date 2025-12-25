<?php
/* --------------------------------------------------------------
   ModuleDetails.php 2018-08-01
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
 * Class ModuleDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class ModuleDetails
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var bool
     */
    private $installed;
    
    /**
     * @var bool|null
     */
    private $enabled;
    
    
    /**
     * ModuleDetails constructor.
     *
     * @param string    $name
     * @param bool      $installed
     * @param bool|null $enabled
     */
    public function __construct($name, $installed, $enabled)
    {
        $this->name      = $name;
        $this->installed = $installed;
        $this->enabled   = $enabled;
    }
    
    
    /**
     * Creates and returns a new ModuleDetails instance.
     *
     * @param string    $name
     * @param bool      $installed
     * @param bool|null $enabled
     *
     * @return ModuleDetails
     */
    static function create($name, $installed, $enabled)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name can not be empty.');
        }
        
        return new self($name, $installed, $enabled);
    }
    
    
    /**
     * Returns the name of the module.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the installed status of the module.
     *
     * @return bool Returns true, if the module is installed, otherwise false will be returned.
     */
    public function installed()
    {
        return $this->installed;
    }
    
    
    /**
     * Returns the enabled status of the mdoule.
     *
     * @return bool|null Null will be returned if its a module center module. For all other modules true will be
     *                   returned, if the module is enabled and false will be returned, if the module is not enabled.
     */
    public function enabled()
    {
        return $this->enabled;
    }
}