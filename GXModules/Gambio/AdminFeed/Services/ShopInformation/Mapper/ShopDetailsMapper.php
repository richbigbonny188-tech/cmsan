<?php
/* --------------------------------------------------------------
   ShopDetailsMapper.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Mapper;

use Gambio\AdminFeed\Services\ShopInformation\Reader\ShopDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;

/**
 * Class ShopDetailsMapper
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Mapper
 */
class ShopDetailsMapper
{
    /**
     * @var ShopDetailsReader
     */
    private $reader;
    
    
    /**
     * ShopDetailsMapper constructor.
     *
     * @param ShopDetailsReader $reader
     */
    public function __construct(ShopDetailsReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * Returns the shop details.
     *
     * @return ShopDetails
     */
    public function getShopDetails()
    {
        return new ShopDetails($this->reader->getVersion(),
                               $this->reader->getUrl(),
                               $this->reader->getKey(),
                               $this->reader->getLanguages(),
                               $this->reader->getDefaultLanguage(),
                               $this->reader->getCountries());
    }
}