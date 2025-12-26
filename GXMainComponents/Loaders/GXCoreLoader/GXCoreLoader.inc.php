<?php
/* --------------------------------------------------------------
  GXCoreLoader.inc.php 2023-07-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

use Doctrine\DBAL\Connection;
use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceFactoryInterface;
use Gambio\GX\Services\System\ThemeSettings\Factories\ThemeSettingsServiceFactory;
use Gambio\MainComponents\Services\Core\AdditionalFields\AdditionalFieldsServiceFactory;
use Gambio\MainComponents\Services\Core\AdditionalProductFields\AdditionalProductFieldsServiceFactory;
use Gambio\ProductImageList\CreateService\Factories\ProductImageListCreateServiceFactory;
use Gambio\ProductImageList\CreateService\Interfaces\ProductImageListCreateServiceFactoryInterface;
use Gambio\ProductImageList\DeleteService\Factories\ProductImageListProductDeleteServiceFactory;
use Gambio\ProductImageList\DeleteService\Interfaces\ProductImageListDeleteServiceFactoryInterface;
use Gambio\ProductImageList\ReadService\Factories\ProductImageListReadServiceFactory;
use Gambio\ProductImageList\ReadService\Interfaces\ProductImageListReadServiceFactoryInterface;
use Gambio\ProductImageList\UpdateService\Factories\ProductImageListUpdateServiceFactory;
use Gambio\ProductImageList\UpdateService\Interfaces\ProductImageListUpdateServiceFactoryInterface;
use Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Configurations\ShopPaths as ProductImageShopPaths;
use Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Factories\ReadServiceFactory as ProductImageReadServiceFactory;
use Gambio\Shop\SellingUnit\ProductInformation\Services\ProductImage\Interfaces\ReadServiceInterface as ProductImageReadServiceInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

MainFactory::load_class('GXCoreLoaderInterface');

/**
 * Class GXCoreLoader
 *
 * @category    System
 * @package     Loaders
 * @subpackage  GXCoreLoader
 */
class GXCoreLoader implements GXCoreLoaderInterface
{
    /**
     * Contains the loader settings.
     *
     * @var GXCoreLoaderSettingsInterface
     */
    protected $gxCoreLoaderSettings;
    
    /**
     * Database Layer Object
     *
     * @var CI_DB_query_builder
     */
    protected $ciDatabaseQueryBuilder;
    
    /**
     * Database Utility Helper
     *
     * @var CI_DB_utility
     */
    protected $ciDatabaseUtilityHelper;
    
    /**
     * Database Forge Helper
     *
     * @var CI_DB_forge
     */
    protected $ciDatabaseForgeHelper;
    
    /**
     * Factory for OrderService Objects
     *
     * @var AbstractOrderServiceFactory
     */
    protected $orderServiceFactory;
    
    /**
     * Factory to create objects of the customer service.
     *
     * @var CustomerServiceFactory
     */
    protected $customerServiceFactory;
    
    /**
     * Factory to create objects of the email service.
     *
     * @var EmailFactory
     */
    protected $emailFactory;
    
    /**
     * Factory to create objects of the category service.
     *
     * @var CategoryServiceFactory
     */
    protected $categoryServiceFactory;
    
    /**
     * Factory to create objects of the product service.
     *
     * @var ProductServiceFactory
     */
    protected $productServiceFactory;
    
    /**
     * Factory to create objects of the addon value service.
     *
     * @var AddonValueServiceFactory
     */
    protected $addonValueServiceFactory;
    
    /**
     * Factory to create objects of the invoice service.
     *
     * @var InvoiceServiceFactory
     */
    protected $invoiceServiceFactory;
    
    /**
     * Factory to create objects of the slider service.
     *
     * @var SliderServiceFactory
     */
    protected $sliderServiceFactory;
    
    /**
     * Factory to create objects of the QuickEdit service.
     *
     * @var QuickEditServiceFactory
     */
    protected $quickEditServiceFactory;
    
    /**
     * Factory to create objects if the OrderStatus service.
     *
     * @var OrderStatusServiceFactory
     */
    protected $orderStatusServiceFactory;
    
    /**
     * Factory to create objects of the version info service.
     *
     * @var VersionInfoServiceFactory
     */
    protected $versionInfoServiceFactory;
    
    /**
     * Factory to create objects of the static seo url service.
     *
     * @var StaticSeoUrlServiceFactory
     */
    protected $staticSeoUrlServiceFactory;
    
    /**
     * Factory to create objects of the vpe service.
     *
     * @var \VPEServiceFactory
     */
    protected $vpeServiceFactory;
    
    /**
     * Factory to create objects of the quantity unit service.
     *
     * @var \QuantityUnitServiceFactory
     */
    protected $quantityUnitServiceFactory;
    
    /**
     * Factory to create objects of the customer group service.
     *
     * @var \CustomerGroupServiceFactory
     */
    protected $customerGroupServiceFactory;
    
    /**
     * Factory to create objects of the customer group service.
     *
     * @var \ManufacturerServiceFactory
     */
    protected $manufacturerServiceFactory;
    
    /**
     * Factory to create objects of the withdrawal service.
     *
     * @var \ReviewServiceFactory
     */
    protected $reviewServiceFactory;
    
    
    /**
     * Factory to create objects of the withdrawal service.
     *
     * @var \WithdrawalServiceFactory
     */
    protected $withdrawalServiceFactory;
    
    /**
     * Factory to create objects of the admin access service.
     *
     * @var \AdminAccessServiceFactory
     */
    protected $adminAccessServiceFactory;
    
    /**
     * Factory to create objects of the shopping cart service.
     *
     * @var \ShoppingCartServiceFactory
     */
    protected $shoppingCartServiceFactory;
    
    /**
     * @var GmConfigurationServiceFactory
     */
    protected $gmConfigurationServiceFactory;
    
    
    /**
     * Factory to create objects of the packing slip service.
     *
     * @var PackingSlipServiceFactory
     */
    protected $packingSlipServiceFactory;
    
    
    /**
     * Factory to create objects of the newsletter subscription service.
     *
     * @var NewsletterSubscriptionServiceFactory
     */
    protected $newsletterSubscriptionServiceFactory;
    
    
    /**
     * Factory to create objects of the personal data service.
     *
     * @var PersonalDataServiceFactory
     */
    protected $personalDataServiceFactory;
    
    /**
     * Factory to create objects of the agreement service.
     *
     * @var AgreementServiceFactory
     */
    protected $agreementServiceFactory;
    
    /**
     * Factory to create objects of the content service.
     *
     * @var ContentServiceFactory
     */
    protected $contentServiceFactory;
    
    /**
     * @var ThemeSettingsServiceFactory
     */
    protected $themeSettingsServiceFactory;
    
    /**
     * The standard DebugBar instance to hold.
     *
     * @var DebugBar
     */
    protected $debugBar;
    
    /**
     * The DebugBar assets.
     *
     * @var array
     */
    protected $debugBarAssets;
    
    /**
     * The ThemeControl.
     *
     * @var \ThemeControl
     */
    protected $themeControl;
    
    /**
     * @var FeaturedProductServiceFactory
     */
    protected $featuredProductServiceFactory;
    
    /**
     * @var ViewSettingsFactory
     */
    protected $viewSettingsServiceFactory;
    
    /**
     * @var PublishedThemeValidationServiceFactoryInterface
     */
    protected $publishedThemeValidationServiceFactory;
    /**
     * @var PurposeReaderServiceFactory
     */
    protected $purposeReaderServiceFactory;
    
    /**
     * @var ProductImageListReadServiceFactory
     */
    protected $productImageListReadServiceFactory;
    /**
     * @var ProductImageListCreateServiceFactory
     */
    protected $productImageListCreateServiceFactory;
    
    /**
     * @var ProductImageListDeleteServiceFactoryInterface
     */
    protected $productImageListDeleteServiceFactory;
    
    /**
     * @var ProductImageListUpdateServiceFactoryInterface
     */
    protected $productImageListUpdateServiceFactory;
    
    /**
     * @var ProductImageReadServiceInterface
     */
    protected $productImageReadService;
    
    /**
     * @var ProductImageInUseServiceFactory
     */
    protected $productImageInUseServiceFactory;

    /**
     * @var ProductListingDisplayService
     */
    protected $productListingDisplayService;

    /**
     * @var string
     */
    protected $shopPath;
    
    /**
     * @var LanguagesReadServiceFactory
     */
    protected $languagesReadServiceFactory;
    
    
    /**
     * Class Constructor
     *
     * @param GXCoreLoaderSettingsInterface $gxCoreLoaderSettings
     */
    public function __construct(GXCoreLoaderSettingsInterface $gxCoreLoaderSettings)
    {
        $this->gxCoreLoaderSettings = $gxCoreLoaderSettings;
        $this->shopPath = realpath(DIR_FS_CATALOG);
        $this->shopPath = str_replace('\\', '/', $this->shopPath) . '/';
    }
    
    
    /**
     * Get the requested server object.
     *
     * @param string $serviceName
     *
     * @return mixed
     *
     * @throws DomainException
     *
     * @todo Delegate to GXServiceFactory
     */
    public function getService($serviceName)
    {
        switch ($serviceName) {
            case 'Customer': // DEPRECATED!!
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $customerService        = $customerServiceFactory->getCustomerService();
                
                return $customerService;
                break;
            case 'CustomerRead':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $customerReadService    = $customerServiceFactory->createCustomerReadService();
                
                return $customerReadService;
                break;
            case 'CustomerWrite':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $customerWriteService   = $customerServiceFactory->createCustomerWriteService();
                
                return $customerWriteService;
                break;
            case 'AddressBook':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $addressBookService     = $customerServiceFactory->getAddressBookService();
                
                return $addressBookService;
                break;
            case 'Country':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $countryService         = $customerServiceFactory->getCountryService();
                
                return $countryService;
                break;
            case 'RegistrationInputValidator':
                $customerServiceFactory     = $this->_getCustomerServiceFactory();
                $registrationInputValidator = $customerServiceFactory->getCustomerRegistrationInputValidatorService();
                
                return $registrationInputValidator;
                break;
            case 'AccountInputValidator':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $accountInputValidator  = $customerServiceFactory->getCustomerAccountInputValidator();
                
                return $accountInputValidator;
                break;
            case 'AddressInputValidator':
                $customerServiceFactory = $this->_getCustomerServiceFactory();
                $accountInputValidator  = $customerServiceFactory->getCustomerAddressInputValidatorService();
                
                return $accountInputValidator;
                break;
            case 'UserConfiguration':
                $db                       = $this->getDatabaseQueryBuilder();
                $userConfigurationReader  = MainFactory::create('UserConfigurationReader', $db);
                $userConfigurationWriter  = MainFactory::create('UserConfigurationWriter', $db);
                $userConfigurationService = MainFactory::create('UserConfigurationService',
                                                                $userConfigurationReader,
                                                                $userConfigurationWriter);
                
                return $userConfigurationService;
                break;
            case 'Statistics':
                $db                = $this->getDatabaseQueryBuilder();
                $xtcPrice          = new xtcPrice($_SESSION['currency'],
                                                  $_SESSION['customers_status']['customers_status_id']);
                $statisticsService = MainFactory::create('StatisticsService', $db, $xtcPrice);
                
                return $statisticsService;
                break;
            case 'Email':
                $emailFactory = $this->_getEmailFactory();
                
                return $emailFactory->createService();
                break;
            case 'OrderObject':
                $factory = $this->_getOrderServiceFactory();
                
                return $factory->createOrderObjectService();
                break;
            case 'OrderRead':
                $factory = $this->_getOrderServiceFactory();
                
                return $factory->createOrderReadService();
                break;
            case 'OrderWrite':
                $factory = $this->_getOrderServiceFactory();
                
                return $factory->createOrderWriteService();
                break;
            case 'Http':
                $httpServiceFactory = MainFactory::create('HttpServiceFactory');
                
                return $httpServiceFactory->createService();
                break;
            case 'CategoryRead':
                $factory = $this->_getCategoryServiceFactory();
                
                return $factory->createCategoryReadService();
                break;
            case 'CategoryWrite':
                $factory = $this->_getCategoryServiceFactory();
                
                return $factory->createCategoryWriteService();
                break;
            case 'CategoryObject':
                $factory = $this->_getCategoryServiceFactory();
                
                return $factory->createCategoryObjectService();
                break;
            case 'AddonValue':
                $factory = $this->_getAddonValueServiceFactory();
                
                return $factory->createAddonValueService();
                break;
            case 'ProductRead':
                $factory = $this->_getProductServiceFactory();
                
                return $factory->createProductReadService();
                break;
            case 'ProductWrite':
                $factory = $this->_getProductServiceFactory();
                
                return $factory->createProductWriteService();
                break;
            case 'ProductObject':
                $factory = $this->_getProductServiceFactory();
                
                return $factory->createProductObjectService();
                break;
            case 'SharedShoppingCart':
                $factory = $this->_getShoppingCartFactory();
                
                return $factory->createSharedShoppingCartService();
                break;
            case 'InfoBox':
                $db             = $this->getDatabaseQueryBuilder();
                $infoBoxFactory = MainFactory::create('InfoBoxFactory', $db);
                
                return $infoBoxFactory->createInfoBoxService();
                break;
            case 'InvoiceArchiveRead':
                $invoiceServiceFactory = $this->_getInvoiceServiceFactory();
                
                return $invoiceServiceFactory->createInvoiceArchiveReadService();
                breaK;
            case 'InvoiceArchiveWrite':
                $invoiceServiceFactory = $this->_getInvoiceServiceFactory();
                
                return $invoiceServiceFactory->createInvoiceArchiveWriteService();
                breaK;
            case 'Auth':
                $authFactory = $this->_getAuthServiceFactory();
                
                return $authFactory->createAuthService();
                break;
            case 'SliderRead':
                $sliderFactory = $this->_getSliderServiceFactory();
                
                return $sliderFactory->createSliderReadService();
                break;
            case 'SliderWrite':
                $sliderFactory = $this->_getSliderServiceFactory();
                
                return $sliderFactory->createSliderWriteService();
                break;
            case 'QuickEdit':
                $quickEditFactory = $this->_getQuickEditServiceFactory();
                
                return $quickEditFactory->createQuickEditService();
                break;
            case 'OrderStatus':
                $orderStatusFactory = $this->_getOrderStatusServiceFactory();
                
                return $orderStatusFactory->createService();
                break;
            case 'VersionInfo':
                /** @var VersionInfoServiceFactory $versionInfoServiceFactory */ $versionInfoServiceFactory = $this->_getVersionInfoServiceFactory();
                
                return $versionInfoServiceFactory->createVersionInfoService();
            case 'StaticSeoUrlRead':
                $staticSeoUrlServiceFactory = $this->_getStaticSeoUrlServiceFactory();
                
                return $staticSeoUrlServiceFactory->createStaticSeoUrlReadService();
                break;
            case 'StaticSeoUrlWrite':
                $staticSeoUrlServiceFactory = $this->_getStaticSeoUrlServiceFactory();
                
                return $staticSeoUrlServiceFactory->createStaticSeoUrlWriteService();
                break;
            case 'VPERead':
                $vpeServiceFactory = $this->_getVpeServiceFactory();
                
                return $vpeServiceFactory->createVpeReadService();
                break;
            case 'VPEWrite':
                $vpeServiceFactory = $this->_getVpeServiceFactory();
                
                return $vpeServiceFactory->createVpeWriteService();
                break;
            case 'QuantityUnitRead':
                $quantityUnitServiceFactory = $this->_getQuantityUnitServiceFactory();
                
                return $quantityUnitServiceFactory->createReadService();
                break;
            case 'QuantityUnitWrite':
                $quantityUnitServiceFactory = $this->_getQuantityUnitServiceFactory();
                
                return $quantityUnitServiceFactory->createWriteService();
                break;
            case 'CustomerGroupRead':
                $customerGroupServiceFactory = $this->_getCustomerGroupServiceFactory();
                
                return $customerGroupServiceFactory->createReadService();
                break;
            case 'CustomerGroupWrite':
                $customerGroupServiceFactory = $this->_getCustomerGroupServiceFactory();
                
                return $customerGroupServiceFactory->createWriteService();
                break;
            case 'FeaturedProductRead':
                $featuredProductServiceFactory = $this->_getFeaturedProductServiceFactory();
                
                return $featuredProductServiceFactory->createReadService();
                break;
            case 'ManufacturerRead':
                $manufacturerServiceFactory = $this->_getManufacturerServiceFactory();
                
                return $manufacturerServiceFactory->createReadService();
                break;
            case 'ManufacturerWrite':
                $manufacturerServiceFactory = $this->_getManufacturerServiceFactory();
                
                return $manufacturerServiceFactory->createWriteService();
                break;
            case 'AdminAccess':
                $adminAccessServiceFactory = $this->_getAdminAccessServiceFactory();
                
                return $adminAccessServiceFactory->createAdminAccessService();
                break;
            case 'WithdrawalRead':
                $withdrawalServiceFactory = $this->_getWithdrawalServiceFactory();
                
                return $withdrawalServiceFactory->createReadService();
                break;
            case 'WithdrawalWrite':
                $withdrawalServiceFactory = $this->_getWithdrawalServiceFactory();
                
                return $withdrawalServiceFactory->createWriteService();
                break;
            case 'ReviewRead':
                $reviewServiceFactory = $this->_getReviewServiceFactory();
                
                return $reviewServiceFactory->createReadService();
                break;
            case 'ReviewWrite':
                $reviewServiceFactory = $this->_getReviewServiceFactory();
                
                return $reviewServiceFactory->createWriteService();
                break;
            case 'PackingSlip':
                $factory = $this->_getPackingSlipFactory();
                
                return $factory->createPackingSlipService();
            case 'ShoppingCart':
                $factory = $this->_getShoppingCartFactory();
                
                return $factory->createShoppingCartService();
            case 'NewsletterSubscription':
                $factory = $this->_getNewsletterSubscriptionServiceFactory();
                
                return $factory->createService();
            case 'PersonalData':
                $factory = $this->_getPersonalDataFactory();
                
                return $factory->createService();
            case 'AgreementWrite':
                $factory = $this->_getAgreementServiceFactory();
                
                return $factory->createWriteService();
            case 'AgreementRead':
                $factory = $this->_getAgreementServiceFactory();
                
                return $factory->createReadService();
            case 'CronTaskManager':
                return new CronTaskManagerService();
            
            case 'Theme':
                $shopRoot = new ExistingDirectory($this->shopPath);
                
                return ThemeServiceFactory::createThemeService($shopRoot);
            
            case 'ContentWrite':
                
                return $this->contentServiceFactory()->createWriteService();
            
            case 'ContentRead' :
                
                return $this->contentServiceFactory()->createReadService();
            
            case 'ContentDeleter' :
                
                return $this->contentServiceFactory()->deleter();
            
            case 'GmConfiguration' :
                
                return $this->gmConfigurationServiceFactory()->service();
            
            case 'ThemeSettings' :
                return $this->themeSettingsServiceFactory()->service();
            
            case 'ViewSettings' :
                return $this->viewSettingsServiceFactory()->service();
                
            case 'PublishedThemeValidation' :
                return $this->publishedThemeValidationServiceFactory()->service();
                
            case 'ProductImageInUse':
                return $this->productImageInUseServiceFactory()->service();

            case 'ProductListingDisplayService':
                return $this->productListingDisplayService();

            case 'ProductImageListRead' :
                return $this->productImageListReadServiceFactory()->service();
    
            case 'ProductImageListCreate' :
                return $this->productImageListCreateServiceFactory()->service();
                
            case 'ProductImageListUpdate' :
                return $this->productImageListUpdateServiceFactory()->service();
                
            case 'ProductImageListDelete' :
                return $this->productImageListDeleteServiceFactory()->createService();
            
            case 'ProductImageRead' :
                return $this->productImageReadService();
                
            case 'PurposeReader' :
                return $this->purposeReaderServiceFactory()->service();
                
            case 'LanguagesRead' :
                return $this->languageReadServiceFactory()->service();
                
            case 'AdditionalFieldsRead' :
                return $this->additionalFieldServiceFactory()->createReadService();
                
            case 'AdditionalFieldsWrite' :
                return $this->additionalFieldServiceFactory()->createWriteService();
                
            case 'AdditionalFieldsFactory' :
                return $this->additionalFieldServiceFactory()->factory();
    
            case 'AdditionalProductFieldRead' :
                return $this->additionalProductFieldServiceFactory()->createReadService();
    
            case 'AdditionalProductFieldWrite' :
                return $this->additionalProductFieldServiceFactory()->createWriteService();
    
            case 'AdditionalProductFieldFactory' :
                return $this->additionalProductFieldServiceFactory()->factory();

            case 'ProductListingService' :
                return $this->productListingDisplayService();
                
            case 'OrderMail':
                return MainFactory::create(OrderMailService::class);
                
            default:
                throw new DomainException('Unknown service: ' . htmlentities($serviceName));
        }
    }
    
    
    /**
     * @return ProductImageReadServiceInterface
     */
    protected function productImageReadService(): ProductImageReadServiceInterface
    {
        if ($this->productImageReadService === null) {
    
            if (defined('ENABLE_SSL')) {
        
                $shopLocalPath = $this->shopPath;
                $shopWebPath   = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
            } else {
        
                $shopLocalPath = $this->shopPath;
                $shopWebPath   = (ENABLE_SSL_CATALOG === 'true' ? HTTPS_CATALOG_SERVER : HTTP_CATALOG_SERVER) . DIR_WS_CATALOG;
            }
    
            $shopPaths = new ProductImageShopPaths($shopLocalPath, $shopWebPath);
            $factory   = new ProductImageReadServiceFactory($shopPaths, $this->getDatabaseQueryBuilder());
    
            $this->productImageReadService = $factory->service();
        }
        
        return $this->productImageReadService;
    }
    
    
    /**
     * @return ProductImageListUpdateServiceFactoryInterface
     */
    protected function productImageListUpdateServiceFactory(): ProductImageListUpdateServiceFactoryInterface
    {
    	if($this->productImageListUpdateServiceFactory === null) {
    	
    		$this->productImageListUpdateServiceFactory = new ProductImageListUpdateServiceFactory($this->getDatabaseQueryBuilder());
    	}
    	
    	return $this->productImageListUpdateServiceFactory;
    }
    
    /**
     * @return ProductImageListDeleteServiceFactoryInterface
     */
    protected function productImageListDeleteServiceFactory(): ProductImageListDeleteServiceFactoryInterface
    {
    	if($this->productImageListDeleteServiceFactory === null) {
    	
    		$this->productImageListDeleteServiceFactory = new ProductImageListProductDeleteServiceFactory($this->getDatabaseQueryBuilder());
    	}
    	
    	return $this->productImageListDeleteServiceFactory;
    }
    
    /**
     * @return PurposeReaderServiceFactoryInterface
     */
    protected function purposeReaderServiceFactory(): PurposeReaderServiceFactoryInterface
    {
    	if($this->purposeReaderServiceFactory === null) {
    	
    		$this->purposeReaderServiceFactory = new PurposeReaderServiceFactory($this->getDatabaseQueryBuilder());
    	}
    	
    	return $this->purposeReaderServiceFactory;
    }
    
    /**
     * @return ProductImageListCreateServiceFactoryInterface
     */
    protected function productImageListCreateServiceFactory(): ProductImageListCreateServiceFactoryInterface
    {
    	if($this->productImageListCreateServiceFactory === null) {
    	
    		$this->productImageListCreateServiceFactory = new ProductImageListCreateServiceFactory($this->getDatabaseQueryBuilder());
    	}
    	
    	return $this->productImageListCreateServiceFactory;
    }
    
    
    /**
     * @return ProductImageListReadServiceFactoryInterface
     */
    protected function productImageListReadServiceFactory(): ProductImageListReadServiceFactoryInterface
    {
        if ($this->productImageListReadServiceFactory === null) {
    
            if (defined('ENABLE_SSL')) {
    
                $shopWebPath   = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
            } else {

                $shopWebPath   = (ENABLE_SSL_CATALOG === 'true' ? HTTPS_CATALOG_SERVER : HTTP_CATALOG_SERVER) . DIR_WS_CATALOG;
            }
    
            $shopWebPath   .= 'images/product_images/original_images/';
            $shopLocalPath = $this->shopPath . 'images/product_images/original_images/';
    
            $this->productImageListReadServiceFactory = new ProductImageListReadServiceFactory($this->getDatabaseQueryBuilder(),
                                                                                               $shopWebPath,
                                                                                               $shopLocalPath);
        }
        
        return $this->productImageListReadServiceFactory;
    }
    
    /**
     * @return PublishedThemeValidationServiceFactoryInterface
     */
    protected function publishedThemeValidationServiceFactory(): PublishedThemeValidationServiceFactoryInterface
    {
        if ($this->publishedThemeValidationServiceFactory === null) {
            $permissionMap = [
                'file' => [
                    'public'  => 0777,
                    'private' => 0700,
                ],
                'dir'  => [
                    'public'  => 0777,
                    'private' => 0700,
                ]
            ];
            $visibility = PortableVisibilityConverter::fromArray($permissionMap);
    
            $filesystemAdapter = new LocalFilesystemAdapter(SHOP_ROOT, $visibility, LOCK_EX, LocalFilesystemAdapter::DISALLOW_LINKS);
            $filesystem        = new Filesystem($filesystemAdapter);
            $cacheControl      = MainFactory::create(CacheControl::class);
            $adapter           = MainFactory::create(FilesystemAdapter::class, $filesystem);
            $shopPaths         = MainFactory::create(ShopPaths::class,
                                                     (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG);
            
            $this->publishedThemeValidationServiceFactory = MainFactory::create(PublishedThemeValidationServiceFactory::class, $adapter, $shopPaths, $cacheControl);
        }
        
        return $this->publishedThemeValidationServiceFactory;
    }
    
    /**
     * Method depends on CodeIgniter database library
     *
     * @return CI_DB_query_builder
     *
     * @todo Check connection errors
     * @todo Escape special characters in mysqli connection string.
     * @todo Use the GXDatabaseAccessorInterface.
     */
    public function getDatabaseQueryBuilder()
    {
        if ($this->ciDatabaseQueryBuilder !== null) {
            return $this->ciDatabaseQueryBuilder;
        }
        
        $connectionString = $this->_getDatabaseConnectionString();
        
        $this->ciDatabaseQueryBuilder = CIDB($connectionString);

        mysqli_report(MYSQLI_REPORT_OFF);

        // @todo Remove the following block when the shop is totally ready for MySQL strict mode.
        if (isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger'])
            && $GLOBALS['coo_debugger']->is_enabled('enable_mysql_strict_mode')) {
            $this->ciDatabaseQueryBuilder->query('SET SESSION sql_mode = "ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,'
                                                 . 'NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,'
                                                 . 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"');
        } else {
            $this->ciDatabaseQueryBuilder->query('SET SESSION sql_mode = ""');
        }
        
        return $this->ciDatabaseQueryBuilder;
    }
    
    
    /**
     * Returns the DebugBar instance.
     *
     * Method depends on the PHP DebugBar library.
     *
     * @return DebugBar
     */
    public function getDebugBar()
    {
        if ($this->debugBar !== null) {
            return $this->debugBar;
        }
        
        $this->debugBar = MainFactory::create('DebugBar');
        
        return $this->debugBar;
    }
    
    
    /**
     * Returns array with the <head> and <body> HTML assets.
     *
     * Method depends on the PHP DebugBar library.
     *
     * @return array
     */
    public function getDebugBarAssets()
    {
        if ($this->debugBarAssets !== null) {
            return $this->debugBarAssets;
        }
        
        $debugBarRenderer = $this->getDebugBar()->getJavascriptRenderer();
        
        $debugBarAssetGroups = $debugBarRenderer->getAssets();
        
        $this->debugBarAssets = [
            'head' => '',
            'body' => '',
        ];
        
        // Assets required for the DebugBar.
        foreach ($debugBarAssetGroups as $debugBarAssetGroup) {
            foreach ($debugBarAssetGroup as $assetAbsolutePath) {
                if (strpos($assetAbsolutePath, 'jquery') !== false) {
                    continue;
                }
                
                // Build the correct relative URL for serving in the browser.
                
                $pathFromVendorFolder = str_replace($this->shopPath, '', str_replace('\\', '/', $assetAbsolutePath));
                $relativePath         = DIR_WS_CATALOG . $pathFromVendorFolder;
                
                // CSS Assets
                if (strpos($assetAbsolutePath, '.css') !== false) {
                    $this->debugBarAssets['head'] .= '<link rel="stylesheet" href="' . $relativePath . '"/>' . PHP_EOL;
                }
                
                // JavaScript Assets
                if (strpos($assetAbsolutePath, '.js') !== false) {
                    $this->debugBarAssets['body'] .= '<script type="text/javascript" src="' . $relativePath
                                                     . '"></script>' . PHP_EOL;
                }
            }
        }
        
        // Add custom DebugBar scripts.
        $this->debugBarAssets['head'] .= '<link rel="stylesheet" href="' . DIR_WS_CATALOG
                                         . 'GXModules/Gambio/DebugBar/Shop/DebugBar.css"/>';
        $this->debugBarAssets['body'] .= '<script type="text/javascript" src="' . DIR_WS_CATALOG
                                         . 'GXModules/Gambio/DebugBar/Shop/DebugBar.js"></script>';
        
        $this->debugBarAssets['body'] .= $debugBarRenderer->render();
        
        return $this->debugBarAssets;
    }
    
    
    /**
     * Method depends on CodeIgniter database library.
     *
     * @return CI_DB_utility
     */
    public function getDatabaseUtilityHelper()
    {
        if ($this->ciDatabaseUtilityHelper !== null) {
            return $this->ciDatabaseUtilityHelper;
        }
        
        $connectionString = $this->_getDatabaseConnectionString();
        
        $this->ciDatabaseUtilityHelper = CIDBUtils($connectionString);
        
        return $this->ciDatabaseUtilityHelper;
    }
    
    
    /**
     * Method depends on CodeIgniter database library.
     *
     * @return CI_DB_forge
     */
    public function getDatabaseForgeHelper()
    {
        if ($this->ciDatabaseForgeHelper !== null) {
            return $this->ciDatabaseForgeHelper;
        }
        
        $connectionString = $this->_getDatabaseConnectionString();
        
        $this->ciDatabaseForgeHelper = CIDBForge($connectionString);
        
        return $this->ciDatabaseForgeHelper;
    }
    
    
    /**
     * Returns an instance of the ThemeControl.
     *
     * @return \ThemeControl
     */
    public function getThemeControl()
    {
        if ($this->themeControl === null) {
            $this->themeControl = MainFactory::create('ThemeControl',
                                                      $this->viewSettingsServiceFactory()->service()->get());
        }
        
        return $this->themeControl;
    }
    
    
    /**
     * Get connection string for CodeIgniter libraries.
     *
     * @return string
     */
    protected function _getDatabaseConnectionString()
    {
        $dbUser     = $this->gxCoreLoaderSettings->getDatabaseUser();
        $dbPassword = $this->gxCoreLoaderSettings->getDatabasePassword();
        $dbServer   = $this->gxCoreLoaderSettings->getDatabaseServer();
        $dbName     = $this->gxCoreLoaderSettings->getDatabaseName();
        $dbSocket   = $this->gxCoreLoaderSettings->getDatabaseSocket() ? '?socket='
                                                                         . $this->gxCoreLoaderSettings->getDatabaseSocket() : '';
        
        $connectionString = 'mysqli://' . $dbUser . ':' . $dbPassword . '@' . $dbServer . '/' . $dbName . $dbSocket;
        
        return $connectionString;
    }
    
    
    /**
     * Get a customer service factory object.
     *
     * @return CustomerServiceFactory
     */
    protected function _getCustomerServiceFactory()
    {
        if (null === $this->customerServiceFactory) {
            $ciDatabaseQueryBuilder       = $this->getDatabaseQueryBuilder();
            $this->customerServiceFactory = MainFactory::create('CustomerServiceFactory', $ciDatabaseQueryBuilder);
        }
        
        return $this->customerServiceFactory;
    }
    
    
    /**
     * Get an email factory object.
     *
     * @return EmailFactory
     */
    protected function _getEmailFactory()
    {
        if (null === $this->emailFactory) {
            $db                 = $this->getDatabaseQueryBuilder();
            $this->emailFactory = MainFactory::create('EmailFactory', $db);
        }
        
        return $this->emailFactory;
    }
    
    
    /**
     * Get an order service factory object.
     *
     * @return AbstractOrderServiceFactory
     */
    protected function _getOrderServiceFactory()
    {
        if ($this->orderServiceFactory === null) {
            $db                        = $this->getDatabaseQueryBuilder();
            $this->orderServiceFactory = MainFactory::create('OrderServiceFactory', $db);
        }
        
        return $this->orderServiceFactory;
    }
    
    
    /**
     * Get a category service factory.
     *
     * @return CategoryServiceFactory
     */
    protected function _getCategoryServiceFactory()
    {
        if (null === $this->categoryServiceFactory) {
            $db                           = $this->getDatabaseQueryBuilder();
            $settings                     = MainFactory::create('EnvCategoryServiceSettings');
            $seoBoost                     = MainFactory::create_object('GMSEOBoost', [], true);
            $this->categoryServiceFactory = MainFactory::create('CategoryServiceFactory', $db, $settings, $seoBoost);
        }
        
        return $this->categoryServiceFactory;
    }
    
    
    /**
     * Get a product service factory
     *
     * @return ProductServiceFactory
     */
    protected function _getProductServiceFactory()
    {
        if (null === $this->productServiceFactory) {
            $db                          = $this->getDatabaseQueryBuilder();
            $this->productServiceFactory = MainFactory::create('ProductServiceFactory', $db);
        }
        
        return $this->productServiceFactory;
    }
    
    
    /**
     * Get an addon value service factory.
     *
     * @return AddonValueServiceFactory
     */
    protected function _getAddonValueServiceFactory()
    {
        if (null === $this->addonValueServiceFactory) {
            $db                             = $this->getDatabaseQueryBuilder();
            $this->addonValueServiceFactory = MainFactory::create('AddonValueServiceFactory', $db);
        }
        
        return $this->addonValueServiceFactory;
    }
    
    
    /**
     * Returns the invoice service factory to create objects of the invoice service.
     *
     * @return \InvoiceServiceFactory
     */
    protected function _getInvoiceServiceFactory()
    {
        if (null === $this->invoiceServiceFactory) {
            $db                          = $this->getDatabaseQueryBuilder();
            $this->invoiceServiceFactory = MainFactory::create('InvoiceServiceFactory', $db);
        }
        
        return $this->invoiceServiceFactory;
    }
    
    
    /**
     * Get an auth factory object.
     *
     * @return AuthFactory
     */
    protected function _getAuthServiceFactory()
    {
        $passwordEncryptionSettings = MainFactory::create('PasswordEncryptionSettings');
        
        return MainFactory::create('AuthFactory', $passwordEncryptionSettings);
    }
    
    
    /**
     * Get a slider service factory
     *
     * @return SliderServiceFactory
     */
    protected function _getSliderServiceFactory()
    {
        if (null === $this->sliderServiceFactory) {
            $db                         = $this->getDatabaseQueryBuilder();
            $this->sliderServiceFactory = MainFactory::create('SliderServiceFactory', $db);
        }
        
        return $this->sliderServiceFactory;
    }
    
    
    /**
     * Get a QuickEdit service factory
     *
     * @return QuickEditServiceFactory
     */
    protected function _getQuickEditServiceFactory()
    {
        if (null === $this->quickEditServiceFactory) {
            $db                            = $this->getDatabaseQueryBuilder();
            $this->quickEditServiceFactory = MainFactory::create('QuickEditServiceFactory', $db);
        }
        
        return $this->quickEditServiceFactory;
    }
    
    
    /**
     * Returns a OrderStatus service factory.
     *
     * @return OrderStatusServiceFactory
     */
    protected function _getOrderStatusServiceFactory()
    {
        if (null === $this->orderStatusServiceFactory) {
            $db                              = $this->getDatabaseQueryBuilder();
            $this->orderStatusServiceFactory = MainFactory::create('OrderStatusServiceFactory', $db);
        }
        
        return $this->orderStatusServiceFactory;
    }
    
    
    /**
     * Get a version info service factory
     *
     * @return VersionInfoServiceFactory
     */
    protected function _getVersionInfoServiceFactory()
    {
        if (null === $this->versionInfoServiceFactory) {
            $db                              = $this->getDatabaseQueryBuilder();
            $existingDirectory               = new ExistingDirectory($this->shopPath . '/version_info');
            $this->versionInfoServiceFactory = MainFactory::create('VersionInfoServiceFactory',
                                                                   $db,
                                                                   $existingDirectory);
        }
        
        return $this->versionInfoServiceFactory;
    }
    
    
    /**
     * Get a static seo url service factory
     *
     * @return StaticSeoUrlServiceFactory
     */
    protected function _getStaticSeoUrlServiceFactory()
    {
        if (null === $this->staticSeoUrlServiceFactory) {
            $db                               = $this->getDatabaseQueryBuilder();
            $this->staticSeoUrlServiceFactory = MainFactory::create('StaticSeoUrlServiceFactory', $db);
        }
        
        return $this->staticSeoUrlServiceFactory;
    }
    
    
    /**
     * Get a vpe service factory.
     *
     * @return \VPEServiceFactory
     */
    protected function _getVpeServiceFactory()
    {
        if (null === $this->vpeServiceFactory) {
            $db                      = $this->getDatabaseQueryBuilder();
            $this->vpeServiceFactory = MainFactory::create('VPEServiceFactory', $db);
        }
        
        return $this->vpeServiceFactory;
    }
    
    
    /**
     * Get a quantity unit service factory.
     *
     * @return \QuantityUnitServiceFactory
     */
    protected function _getQuantityUnitServiceFactory()
    {
        if (null === $this->quantityUnitServiceFactory) {
            $db                               = $this->getDatabaseQueryBuilder();
            $this->quantityUnitServiceFactory = MainFactory::create('QuantityUnitServiceFactory', $db);
        }
        
        return $this->quantityUnitServiceFactory;
    }
    
    
    /**
     * Get a customer group service factory.
     *
     * @return \CustomerGroupServiceFactory
     */
    protected function _getCustomerGroupServiceFactory()
    {
        if (null === $this->customerGroupServiceFactory) {
            $db                                = $this->getDatabaseQueryBuilder();
            $this->customerGroupServiceFactory = MainFactory::create('CustomerGroupServiceFactory', $db);
        }
        
        return $this->customerGroupServiceFactory;
    }
    
    
    /**
     * Get a featured product service factory.
     *
     * @return FeaturedProductServiceFactory
     */
    protected function _getFeaturedProductServiceFactory()
    {
        if ($this->featuredProductServiceFactory === null) {
            $db                                  = $this->getDatabaseQueryBuilder();
            $isCustomerGroupCheckActive          = new BoolType(GROUP_CHECK === 'true');
            $this->featuredProductServiceFactory = MainFactory::create(FeaturedProductServiceFactory::class,
                                                                       $db,
                                                                       $isCustomerGroupCheckActive);
        }
        
        return $this->featuredProductServiceFactory;
    }
    
    
    /**
     * Get a customer group service factory.
     *
     * @return \ManufacturerServiceFactory
     */
    protected function _getManufacturerServiceFactory()
    {
        if (null === $this->manufacturerServiceFactory) {
            $db                               = $this->getDatabaseQueryBuilder();
            $this->manufacturerServiceFactory = MainFactory::create('ManufacturerServiceFactory', $db);
        }
        
        return $this->manufacturerServiceFactory;
    }
    
    
    /**
     * Get a review service factory.
     *
     * @return \ReviewServiceFactory
     */
    protected function _getReviewServiceFactory()
    {
        if (null === $this->reviewServiceFactory) {
            $db                         = $this->getDatabaseQueryBuilder();
            $this->reviewServiceFactory = MainFactory::create('ReviewServiceFactory', $db);
        }
        
        return $this->reviewServiceFactory;
    }
    
    
    /**
     * Get a customer group service factory.
     *
     * @return \WithdrawalServiceFactory
     */
    protected function _getWithdrawalServiceFactory()
    {
        if (null === $this->withdrawalServiceFactory) {
            $db                             = $this->getDatabaseQueryBuilder();
            $this->withdrawalServiceFactory = MainFactory::create('WithdrawalServiceFactory', $db);
        }
        
        return $this->withdrawalServiceFactory;
    }
    
    
    /**
     * Get a admin access service factory.
     *
     * @return \AdminAccessServiceFactory
     */
    protected function _getAdminAccessServiceFactory()
    {
        if (null === $this->adminAccessServiceFactory) {
            $db                              = $this->getDatabaseQueryBuilder();
            $this->adminAccessServiceFactory = MainFactory::create('AdminAccessServiceFactory', $db);
        }
        
        return $this->adminAccessServiceFactory;
    }
    
    
    /**
     * Get a shopping cart service factory.
     *
     * @return \ShoppingCartServiceFactory
     */
    protected function _getShoppingCartFactory()
    {
        if (null === $this->shoppingCartServiceFactory) {
            $db                               = $this->getDatabaseQueryBuilder();
            $this->shoppingCartServiceFactory = MainFactory::create('ShoppingCartServiceFactory', $db);
        }
        
        return $this->shoppingCartServiceFactory;
    }
    
    
    /**
     * Get a packing slip service factory.
     *
     * @return \PackingSlipServiceFactory
     */
    protected function _getPackingSlipFactory()
    {
        if (null === $this->packingSlipServiceFactory) {
            $db                              = $this->getDatabaseQueryBuilder();
            $this->packingSlipServiceFactory = MainFactory::create('PackingSlipServiceFactory', $db);
        }
        
        return $this->packingSlipServiceFactory;
    }
    
    
    /**
     * Get a newsletter subscription service factory
     *
     * @return \NewsletterSubscriptionServiceFactory
     */
    protected function _getNewsletterSubscriptionServiceFactory()
    {
        if (null === $this->newsletterSubscriptionServiceFactory) {
            $db                                         = $this->getDatabaseQueryBuilder();
            $this->newsletterSubscriptionServiceFactory = MainFactory::create('NewsletterSubscriptionServiceFactory',
                                                                              $db);
        }
        
        return $this->newsletterSubscriptionServiceFactory;
    }
    
    
    /**
     * Get a personal data service factory.
     *
     * @return \PersonalDataServiceFactory
     *
     * @throws DomainException
     */
    protected function _getPersonalDataFactory()
    {
        if (null === $this->personalDataServiceFactory) {
            $this->personalDataServiceFactory = MainFactory::create('PersonalDataServiceFactory',
                                                                    $this->getService('OrderRead'),
                                                                    $this->getService('OrderWrite'),
                                                                    $this->getService('CustomerRead'),
                                                                    $this->getService('Customer'),
                                                                    $this->getService('Email'),
                                                                    $this->getService('WithdrawalRead'),
                                                                    $this->getService('WithdrawalWrite'),
                                                                    $this->getService('ReviewRead'),
                                                                    $this->getService('ReviewWrite'),
                                                                    $this->getService('ShoppingCart'),
                                                                    $this->getService('SharedShoppingCart'),
                                                                    $this->getService('NewsletterSubscription'),
                                                                    $this->getService('AddressBook'),
                                                                    $this->getService('InvoiceArchiveRead'),
                                                                    $this->getService('InvoiceArchiveWrite'),
                                                                    $this->getService('PackingSlip'),
                                                                    $this->getService('AgreementWrite'),
                                                                    $this->getService('AgreementRead'),
                                                                    MainFactory::create('PersonalDataXmlSerializer'));
        }
        
        return $this->personalDataServiceFactory;
    }
    
    
    /**
     * Get an agreement service factory.
     *
     * @return \AgreementServiceFactory
     *
     * @throws DomainException
     */
    protected function _getAgreementServiceFactory()
    {
        if (null === $this->agreementServiceFactory) {
            $this->agreementServiceFactory = MainFactory::create('AgreementServiceFactory',
                                                                 $this->getDatabaseQueryBuilder());
        }
        
        return $this->agreementServiceFactory;
    }
    
    
    /**
     * Get the content service factory
     *
     * @return ContentServiceFactory
     *
     * @throws DomainException
     */
    protected function contentServiceFactory()
    {
        if (null === $this->contentServiceFactory) {
            $this->contentServiceFactory = MainFactory::create('ContentServiceFactory',
                                                               $this->getDatabaseQueryBuilder());
        }
        
        return $this->contentServiceFactory;
    }
    
    
    /**
     * @return GmConfigurationServiceFactory
     */
    public function gmConfigurationServiceFactory(): GmConfigurationServiceFactory
    {
        if ($this->gmConfigurationServiceFactory === null) {
            
            $this->gmConfigurationServiceFactory = new GmConfigurationServiceFactory($this->getDatabaseQueryBuilder());
        }
        
        return $this->gmConfigurationServiceFactory;
    }
    
    
    /**
     * @return mixed
     */
    protected function themeSettingsServiceFactory()
    {
        if ($this->themeSettingsServiceFactory === null) {
            $permissionMap = [
                'file' => [
                    'public'  => 0777,
                    'private' => 0700,
                ],
                'dir'  => [
                    'public'  => 0777,
                    'private' => 0700,
                ],
            ];
            $visibility    = PortableVisibilityConverter::fromArray($permissionMap);
    
            $filesystemAdapter = new LocalFilesystemAdapter(SHOP_ROOT . 'themes',
                                                            $visibility,
                                                            LOCK_EX,
                                                            LocalFilesystemAdapter::DISALLOW_LINKS);
            $filesystem        = new Filesystem($filesystemAdapter);
            $adapter           = MainFactory::create(FilesystemAdapter::class, $filesystem);
            $cacheControl      = MainFactory::create(CacheControl::class);
    
            $this->themeSettingsServiceFactory = MainFactory::create(ThemeSettingsServiceFactory::class,
                                                                     $adapter,
                                                                     $this->getDatabaseQueryBuilder(),
                                                                     $cacheControl,
                                                                     $this->getService('Theme'));
        }
        
        return $this->themeSettingsServiceFactory;
    }
    
    
    /**
     * @return mixed
     */
    protected function viewSettingsServiceFactory(): ViewSettingsFactory
    {
        if ($this->viewSettingsServiceFactory === null) {
            $this->viewSettingsServiceFactory = MainFactory::create(ViewSettingsFactory::class,
                                                                    $this->getDatabaseQueryBuilder());
        }
        
        return $this->viewSettingsServiceFactory;
    }
    
    
    /**
     * @return ViewSettings
     */
    public function getViewSettings(): ViewSettings
    {
        if ($this->viewSettings === null) {
            $reader             = MainFactory::create(ViewSettingsReader::class, $this->getDatabaseQueryBuilder());
            $this->viewSettings = $reader->get();
        }
        
        return $this->viewSettings;
    }
    
    
    /**
     * @return ProductImageInUseServiceFactory
     */
    protected function productImageInUseServiceFactory(): ProductImageInUseServiceFactory
    {
        if ($this->productImageInUseServiceFactory === null) {
            
            $this->productImageInUseServiceFactory = new ProductImageInUseServiceFactory;
        }
        
        return $this->productImageInUseServiceFactory;
    }


    /**
     * @return ProductListingDisplayServiceInterface
     */
    protected function productListingDisplayService(): ProductListingDisplayServiceInterface
    {
        if (null === $this->productListingDisplayService) {
            $this->productListingDisplayService = MainFactory::create('ProductListingDisplayServiceFactory')->create();
        }

        return $this->productListingDisplayService;
    }
    
    
    /**
     * @return AdditionalFieldsServiceFactory
     */
    protected function additionalFieldServiceFactory(): AdditionalFieldsServiceFactory
    {
        $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
        
        return new AdditionalFieldsServiceFactory($connection);
    }
    
    
    /**
     * @return AdditionalProductFieldsServiceFactory
     */
    protected function additionalProductFieldServiceFactory(): AdditionalProductFieldsServiceFactory
    {
        $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
        
        return new AdditionalProductFieldsServiceFactory($connection);
    }
    
    /**
     * @return LanguagesReadServiceFactory
     */
    protected function languageReadServiceFactory(): LanguagesReadServiceFactory
    {
        if ($this->languagesReadServiceFactory === null) {
    
            $this->languagesReadServiceFactory = new LanguagesReadServiceFactory($this->getDatabaseQueryBuilder());
        }
        
        return $this->languagesReadServiceFactory;
    }
}
