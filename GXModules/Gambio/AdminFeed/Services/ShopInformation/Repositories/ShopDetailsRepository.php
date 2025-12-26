<?php
/* --------------------------------------------------------------
   ShopDetailsRepository.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Mapper\ShopDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;

/**
 * Class ShopDetailsRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class ShopDetailsRepository
{
    /**
     * @var ShopDetailsMapper
     */
    private $mapper;
    
    
    /**
     * ShopDetailsRepository constructor.
     *
     * @param ShopDetailsMapper $mapper
     */
    public function __construct(ShopDetailsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the shop details.
     *
     * @return ShopDetails
     */
    public function getShopDetails()
    {
        return $this->mapper->getShopDetails();
    }
}