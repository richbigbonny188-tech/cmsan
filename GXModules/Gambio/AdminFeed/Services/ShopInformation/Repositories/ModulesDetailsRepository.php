<?php
/* --------------------------------------------------------------
   ModulesDetailsRepository.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Mapper\ModulesDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModulesDetails;

/**
 * Class ModulesDetailsRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class ModulesDetailsRepository
{
    /**
     * @var ModulesDetailsMapper
     */
    private $mapper;
    
    
    /**
     * ModulesDetailsRepository constructor.
     *
     * @param ModulesDetailsMapper $mapper
     */
    public function __construct(ModulesDetailsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the modules details.
     *
     * @return ModulesDetails
     */
    public function getModulesDetails()
    {
        return $this->mapper->getModulesDetails();
    }
}