<?php
/*--------------------------------------------------------------------------------------------------
    ShopBaseUrl.php 2019-10-14
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
 * Class ShopBaseUrl
 * @package Gambio\StyleEdit\Configurations
 */
class ShopBaseUrl implements ConfigurationValueInterface
{
    protected $url;
    
    
    /**
     * ShopBaseUrl constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }
    
    
    /**
     * @return string
     */
    public function value()
    {
        return $this->url;
    }
}