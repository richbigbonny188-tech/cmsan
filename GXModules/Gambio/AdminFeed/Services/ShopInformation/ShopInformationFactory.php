<?php
/* --------------------------------------------------------------
   ShopInformationFactory.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation;

use CI_DB_query_builder;
use Gambio\AdminFeed\Adapters\GxAdapterTrait;
use Gambio\AdminFeed\CurlClient;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\FileSystemDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\MerchantDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\ModulesDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\ServerDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\ShopDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\TemplateDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Mapper\UpdatesDetailsMapper;
use Gambio\AdminFeed\Services\ShopInformation\Reader\FileSystemDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\MerchantDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\ModulesDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\ServerDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\ShopDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\TemplateDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Reader\UpdatesDetailsReader;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\FileSystemDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\MerchantDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\ModulesDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\ServerDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\ShopDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\ShopInformationRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\TemplateDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Repositories\UpdatesDetailsRepository;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\FileSystemDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\MerchantAddressDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\MerchantDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ModuleDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ModulesDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\MysqlServerDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\PhpServerDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ServerDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ShopDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ShopInformationSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\ThemeDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\UpdateDetailsSerializer;
use Gambio\AdminFeed\Services\ShopInformation\Serializer\UpdatesDetailsSerializer;

/**
 * Class ShopInformationFactory
 *
 * @package Gambio\AdminFeed\Services\ShopInformation
 */
class ShopInformationFactory
{
    use GxAdapterTrait;
    
    
    /**
     * @var CI_DB_query_builder
     */
    private $db;
    
    /**
     * @var Settings
     */
    private $settings;
    
    /**
     * @var ShopInformationService
     */
    private $service;
    
    /**
     * @var ShopInformationRepository
     */
    private $shopInformationRepository;
    
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
     * @var MerchantDetailsRepository
     */
    private $merchantDetailsRepository;
    
    /**
     * @var UpdatesDetailsRepository
     */
    private $updatesDetailsRepository;
    
    /**
     * @var FileSystemDetailsSerializer
     */
    private $fileSystemDetailsSerializer;
    
    /**
     * @var MerchantAddressDetailsSerializer
     */
    private $merchantAddressDetailsSerializer;
    
    /**
     * @var MerchantDetailsSerializer
     */
    private $merchantDetailsSerializer;
    
    /**
     * @var ModuleDetailsSerializer
     */
    private $moduleDetailsSerializer;
    
    /**
     * @var ModulesDetailsSerializer
     */
    private $modulesDetailsSerializer;
    
    /**
     * @var MysqlServerDetailsSerializer
     */
    private $mysqlServerDetailsSerializer;
    
    /**
     * @var PhpServerDetailsSerializer
     */
    private $phpServerDetailsSerializer;
    
    /**
     * @var ServerDetailsSerializer
     */
    private $serverDetailsSerializer;
    
    /**
     * @var ShopDetailsSerializer
     */
    private $shopDetailsSerializer;
    
    /**
     * @var ShopInformationSerializer
     */
    private $shopInformationSerializer;
    
    /**
     * @var ThemeDetailsSerializer
     */
    private $templateDetailsSerializer;
    
    /**
     * @var UpdateDetailsSerializer
     */
    private $updateDetailsSerializer;
    
    /**
     * @var UpdatesDetailsSerializer
     */
    private $updatesDetailsSerializer;
    
    
    /**
     * Returns an instance of the shop information service.
     *
     * @return ShopInformationService
     */
    public function createService()
    {
        if ($this->service === null) {
            $this->db        = $this->gxAdapter()->getQueryBuilder();
            $this->settings  = new Settings();
            $this->hubClient = new HubClient($this->settings, $this->gxAdapter(), new CurlClient());
            
            $this->service = new ShopInformationService($this->createShopInformationRepository());
        }
        
        return $this->service;
    }
    
    
    /**
     * Returns an instance of the file system details serializer.
     *
     * @return FileSystemDetailsSerializer
     */
    public function createFileSystemDetailsSerializer()
    {
        if ($this->fileSystemDetailsSerializer === null) {
            $this->fileSystemDetailsSerializer = new FileSystemDetailsSerializer();
        }
        
        return $this->fileSystemDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the merchant address details serializer.
     *
     * @return MerchantAddressDetailsSerializer
     */
    public function createMerchantAddressDetailsSerializer()
    {
        if ($this->merchantAddressDetailsSerializer === null) {
            $this->merchantAddressDetailsSerializer = new MerchantAddressDetailsSerializer();
        }
        
        return $this->merchantAddressDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the merchant details serializer.
     *
     * @return MerchantDetailsSerializer
     */
    public function createMerchantDetailsSerializer()
    {
        if ($this->merchantDetailsSerializer === null) {
            $this->merchantDetailsSerializer = new MerchantDetailsSerializer($this->createMerchantAddressDetailsSerializer());
        }
        
        return $this->merchantDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the module details serializer.
     *
     * @return ModuleDetailsSerializer
     */
    public function createModuleDetailsSerializer()
    {
        if ($this->moduleDetailsSerializer === null) {
            $this->moduleDetailsSerializer = new ModuleDetailsSerializer();
        }
        
        return $this->moduleDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the modules details serializer.
     *
     * @return ModulesDetailsSerializer
     */
    public function createModulesDetailsSerializer()
    {
        if ($this->modulesDetailsSerializer === null) {
            $this->modulesDetailsSerializer = new ModulesDetailsSerializer($this->createModuleDetailsSerializer());
        }
        
        return $this->modulesDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the mysql server details serializer.
     *
     * @return MysqlServerDetailsSerializer
     */
    public function createMysqlServerDetailsSerializer()
    {
        if ($this->mysqlServerDetailsSerializer === null) {
            $this->mysqlServerDetailsSerializer = new MysqlServerDetailsSerializer();
        }
        
        return $this->mysqlServerDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the php server details serializer.
     *
     * @return PhpServerDetailsSerializer
     */
    public function createPhpServerDetailsSerializer()
    {
        if ($this->phpServerDetailsSerializer === null) {
            $this->phpServerDetailsSerializer = new PhpServerDetailsSerializer();
        }
        
        return $this->phpServerDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the server details serializer.
     *
     * @return ServerDetailsSerializer
     */
    public function createServerDetailsSerializer()
    {
        if ($this->serverDetailsSerializer === null) {
            $this->serverDetailsSerializer = new ServerDetailsSerializer($this->createPhpServerDetailsSerializer(),
                                                                         $this->createMysqlServerDetailsSerializer());
        }
        
        return $this->serverDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the shop details serializer.
     *
     * @return ShopDetailsSerializer
     */
    public function createShopDetailsSerializer()
    {
        if ($this->shopDetailsSerializer === null) {
            $this->shopDetailsSerializer = new ShopDetailsSerializer();
        }
        
        return $this->shopDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the shop information serializer.
     *
     * @return ShopInformationSerializer
     */
    public function createShopInformationSerializer()
    {
        if ($this->shopInformationSerializer === null) {
            $this->shopInformationSerializer = new ShopInformationSerializer($this->createShopDetailsSerializer(),
                                                                             $this->createServerDetailsSerializer(),
                                                                             $this->createModulesDetailsSerializer(),
                                                                             $this->createTemplateDetailsSerializer(),
                                                                             $this->createFileSystemDetailsSerializer(),
                                                                             $this->createUpdatesDetailsSerializer());
        }
        
        return $this->shopInformationSerializer;
    }
    
    
    /**
     * Returns an instance of the template details serializer.
     *
     * @return ThemeDetailsSerializer
     */
    public function createTemplateDetailsSerializer()
    {
        if ($this->templateDetailsSerializer === null) {
            $this->templateDetailsSerializer = new ThemeDetailsSerializer();
        }
        
        return $this->templateDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the update details serializer.
     *
     * @return UpdateDetailsSerializer
     */
    public function createUpdateDetailsSerializer()
    {
        if ($this->updateDetailsSerializer === null) {
            $this->updateDetailsSerializer = new UpdateDetailsSerializer();
        }
        
        return $this->updateDetailsSerializer;
    }
    
    
    /**
     * Returns an instance of the updates details serializer.
     *
     * @return UpdatesDetailsSerializer
     */
    public function createUpdatesDetailsSerializer()
    {
        if ($this->updatesDetailsSerializer === null) {
            $this->updatesDetailsSerializer = new UpdatesDetailsSerializer($this->createUpdateDetailsSerializer());
        }
        
        return $this->updatesDetailsSerializer;
    }
    
    
    /**
     * @return ShopInformationRepository
     */
    private function createShopInformationRepository()
    {
        if ($this->shopInformationRepository === null) {
            $this->shopInformationRepository = new ShopInformationRepository($this->createShopDetailsRepository(),
                                                                             $this->createServerDetailsRepository(),
                                                                             $this->createModulesDetailsRepository(),
                                                                             $this->createTemplateDetailsRepository(),
                                                                             $this->createFileSystemDetailsRepository(),
                                                                             $this->createUpdatesDetailsRepository());
        }
        
        return $this->shopInformationRepository;
    }
    
    
    /**
     * @return ShopDetailsRepository
     */
    private function createShopDetailsRepository()
    {
        if ($this->shopDetailsRepository === null) {
            $reader = new ShopDetailsReader($this->settings, $this->db);
            $mapper = new ShopDetailsMapper($reader);
            
            $this->shopDetailsRepository = new ShopDetailsRepository($mapper);
        }
        
        return $this->shopDetailsRepository;
    }
    
    
    /**
     * @return ServerDetailsRepository
     */
    private function createServerDetailsRepository()
    {
        if ($this->serverDetailsRepository === null) {
            $reader = new ServerDetailsReader($this->db);
            $mapper = new ServerDetailsMapper($reader);
            
            $this->serverDetailsRepository = new ServerDetailsRepository($mapper);
        }
        
        return $this->serverDetailsRepository;
    }
    
    
    /**
     * @return ModulesDetailsRepository
     */
    private function createModulesDetailsRepository()
    {
        if ($this->modulesDetailsRepository === null) {
            $reader = new ModulesDetailsReader($this->settings, $this->db, $this->hubClient);
            $mapper = new ModulesDetailsMapper($reader);
            
            $this->modulesDetailsRepository = new ModulesDetailsRepository($mapper);
        }
        
        return $this->modulesDetailsRepository;
    }
    
    
    /**
     * @return TemplateDetailsRepository
     */
    private function createTemplateDetailsRepository()
    {
        if ($this->templateDetailsRepository === null) {
            $reader = new TemplateDetailsReader($this->settings);
            $mapper = new TemplateDetailsMapper($reader);
            
            $this->templateDetailsRepository = new TemplateDetailsRepository($mapper);
        }
        
        return $this->templateDetailsRepository;
    }
    
    
    /**
     * @return FileSystemDetailsRepository
     */
    private function createFileSystemDetailsRepository()
    {
        if ($this->fileSystemDetailsRepository === null) {
            $reader = new FileSystemDetailsReader($this->settings);
            $mapper = new FileSystemDetailsMapper($reader);
            
            $this->fileSystemDetailsRepository = new FileSystemDetailsRepository($mapper);
        }
        
        return $this->fileSystemDetailsRepository;
    }
    
    
    /**
     * @return MerchantDetailsRepository
     */
    private function createMerchantDetailsRepository()
    {
        if ($this->merchantDetailsRepository === null) {
            $reader = new MerchantDetailsReader($this->db);
            $mapper = new MerchantDetailsMapper($reader);
            
            $this->merchantDetailsRepository = new MerchantDetailsRepository($mapper);
        }
        
        return $this->merchantDetailsRepository;
    }
    
    
    /**
     * @return UpdatesDetailsRepository
     */
    private function createUpdatesDetailsRepository()
    {
        if ($this->updatesDetailsRepository === null) {
            $reader = new UpdatesDetailsReader($this->db);
            $mapper = new UpdatesDetailsMapper($reader);
            
            $this->updatesDetailsRepository = new UpdatesDetailsRepository($mapper);
        }
        
        return $this->updatesDetailsRepository;
    }
}