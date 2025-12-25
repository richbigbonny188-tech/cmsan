<?php
/* --------------------------------------------------------------
   TemplateDetailsRepository.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Mapper\TemplateDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;

/**
 * Class TemplateDetailsRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class TemplateDetailsRepository
{
    /**
     * @var TemplateDetailsMapper
     */
    private $mapper;
    
    
    /**
     * TemplateDetailsRepository constructor.
     *
     * @param TemplateDetailsMapper $mapper
     */
    public function __construct(TemplateDetailsMapper $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns the template details.
     *
     * @return ThemeDetails
     */
    public function getTemplateDetails()
    {
        return $this->mapper->getTemplateDetails();
    }
}