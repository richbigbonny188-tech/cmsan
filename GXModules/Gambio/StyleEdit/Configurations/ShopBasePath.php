<?php
/*--------------------------------------------------------------------------------------------------
    ShopBasePath.php 2019-11-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Configurations;

use Gambio\StyleEdit\ConfigurationValueInterface;

/**
 * Class ShopBasePath
 * @package Gambio\StyleEdit\Configurations
 */
class ShopBasePath implements ConfigurationValueInterface
{
    protected $path;
    
    
    /**
     * ShopBasePath constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @return string
     */
    public function value()
    {
        return $this->path;
    }
}