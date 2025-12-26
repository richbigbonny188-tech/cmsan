<?php
/* --------------------------------------------------------------
   ShopInformationService.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation;

use Gambio\AdminFeed\Services\ShopInformation\Entities\ShopInformation;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\ShopInformationRepository;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\FileSystemDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModulesDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ServerDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;

/**
 * Class ShopInformationService
 *
 * @package Gambio\AdminFeed\Services\ShopInformation
 */
class ShopInformationService
{
    public const CURRENT_SHOP_INFORMATION_VERSION = 2;
    
    /**
     * @var ShopInformationRepository
     */
    private $repository;
    
    
    /**
     * ShopInformationService constructor.
     *
     * @param ShopInformationRepository $repository
     */
    public function __construct(ShopInformationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns the all information of the shop.
     *
     * @return ShopInformation
     */
    public function getShopInformation()
    {
        return ShopInformation::create($this->getShopDetails(),
                                       $this->getServerDetails(),
                                       $this->getModulesDetails(),
                                       $this->getTemplateDetails(),
                                       $this->getFileSystemDetails(),
                                       $this->getUpdatesDetails(),
                                       self::CURRENT_SHOP_INFORMATION_VERSION);
    }
    
    
    /**
     * Returns the shop details of the shop.
     *
     * @return ShopDetails
     */
    public function getShopDetails()
    {
        return $this->repository->getShopDetails();
    }
    
    
    /**
     * Returns the server details of the shop.
     *
     * @return ServerDetails
     */
    public function getServerDetails()
    {
        return $this->repository->getServerDetails();
    }
    
    
    /**
     * Returns the modules details of the shop.
     *
     * @return ModulesDetails
     */
    public function getModulesDetails()
    {
        return $this->repository->getModulesDetails();
    }
    
    
    /**
     * Returns the template details of the shop.
     *
     * @return ThemeDetails
     */
    public function getTemplateDetails()
    {
        return $this->repository->getTemplateDetails();
    }
    
    
    /**
     * Returns the file system details of the shop.
     *
     * @return FileSystemDetails
     */
    public function getFileSystemDetails()
    {
        return $this->repository->getFileSystemDetails();
    }
    
    
    /**
     * Returns the update details of this shop.
     *
     * @return UpdatesDetails
     */
    public function getUpdatesDetails()
    {
        return $this->repository->getUpdatesDetails();
    }
}