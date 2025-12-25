<?php
/* --------------------------------------------------------------
   UpdatesDetailsRepository.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Mapper\UpdatesDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;

/**
 * Class UpdatesDetailsRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class UpdatesDetailsRepository
{
    /**
     * @var UpdatesDetailsMapper
     */
    private $mapper;
    
    
    /**
     * UpdatesDetailsRepository constructor.
     *
     * @param UpdatesDetailsMapper $mapper
     */
    public function __construct(UpdatesDetailsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the updates details.
     *
     * @return UpdatesDetails
     */
    public function getUpdatesDetails()
    {
        return $this->mapper->getUpdatesDetails();
    }
}