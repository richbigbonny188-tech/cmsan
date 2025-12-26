<?php
/* --------------------------------------------------------------
   ShopInformationRepository.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Repositories;

use Gambio\AdminFeed\Services\ShopInformation\Entities\ShopInformation;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\FileSystemDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModulesDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ServerDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;

/**
 * Class ShopInformationRepository
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Repositories
 */
class ShopInformationRepository
{
    /**
     * @var ShopDetailsRepository
     */
    private $shopDetailsRepository;
    
    /**
     * @var ServerDetailsRepository
     */
    private $serverDetailsRepository;
    
    /**
     * @var ModulesDetailsRepository
     */
    private $modulesDetailsRepository;
    
    /**
     * @var TemplateDetailsRepository
     */
    private $templateDetailsRepository;
    
    /**
     * @var FileSystemDetailsRepository
     */
    private $fileSystemDetailsRepository;
    
    /**
     * @var UpdatesDetailsRepository
     */
    private $updatesDetailsRepository;
    
    
    /**
     * ShopInformationRepository constructor.
     *
     * @param ShopDetailsRepository       $shopDetailsRepository
     * @param ServerDetailsRepository     $serverDetailsRepository
     * @param ModulesDetailsRepository    $modulesDetailsRepository
     * @param TemplateDetailsRepository   $templateDetailsRepository
     * @param FileSystemDetailsRepository $fileSystemDetailsRepository
     * @param MerchantDetailsRepository   $merchantDetailsRepository
     * @param UpdatesDetailsRepository    $updatesDetailsRepository
     */
    public function __construct(
        ShopDetailsRepository $shopDetailsRepository,
        ServerDetailsRepository $serverDetailsRepository,
        ModulesDetailsRepository $modulesDetailsRepository,
        TemplateDetailsRepository $templateDetailsRepository,
        FileSystemDetailsRepository $fileSystemDetailsRepository,
        UpdatesDetailsRepository $updatesDetailsRepository
    ) {
        $this->shopDetailsRepository       = $shopDetailsRepository;
        $this->serverDetailsRepository     = $serverDetailsRepository;
        $this->modulesDetailsRepository    = $modulesDetailsRepository;
        $this->templateDetailsRepository   = $templateDetailsRepository;
        $this->fileSystemDetailsRepository = $fileSystemDetailsRepository;
        $this->updatesDetailsRepository    = $updatesDetailsRepository;
    }
    
    
    /**
     * Returns the shop information.
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
                                       $this->getUpdatesDetails());
    }
    
    
    /**
     * Returns the shop details.
     *
     * @return ShopDetails
     */
    public function getShopDetails()
    {
        return $this->shopDetailsRepository->getShopDetails();
    }
    
    
    /**
     * Returns the server details.
     *
     * @return ServerDetails
     */
    public function getServerDetails()
    {
        return $this->serverDetailsRepository->getServerDetails();
    }
    
    
    /**
     * Returns the modules details.
     *
     * @return ModulesDetails
     */
    public function getModulesDetails()
    {
        return $this->modulesDetailsRepository->getModulesDetails();
    }
    
    
    /**
     * Returns templates details.
     *
     * @return ThemeDetails
     */
    public function getTemplateDetails()
    {
        return $this->templateDetailsRepository->getTemplateDetails();
    }
    
    
    /**
     * Returns the file system details.
     *
     * @return FileSystemDetails
     */
    public function getFileSystemDetails()
    {
        return $this->fileSystemDetailsRepository->getFileSystemDetails();
    }
    
    
    /**
     * Returns the updates details.
     *
     * @return UpdatesDetails
     */
    public function getUpdatesDetails()
    {
        return $this->updatesDetailsRepository->getUpdatesDetails();
    }
}