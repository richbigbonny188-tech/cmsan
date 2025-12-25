<?php
/*--------------------------------------------------------------------------------------------------
    DependencyInjector.php 2022-04-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

namespace Gambio\StyleEdit;

use ContentDeleterInterface;
use ContentIdentificationFactory;
use ContentReadServiceInterface;
use ContentWriteServiceInterface;
use CookieConsentPurposeReaderServiceInterface;
use CustomerServiceFactory;
use FilesystemAdapter;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeDatabaseWriterInterface;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeWriteRepositoryInterface;
use Gambio\CookieConsentPanel\Services\Purposes\PurposeWriteService;
use Gambio\CookieConsentPanel\Services\Purposes\Repositories\PurposeDatabaseWriter;
use Gambio\CookieConsentPanel\Services\Purposes\Repositories\PurposeWriteRepository;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\GX\Application;
use Gambio\GX\ApplicationStyleEditApi;
use Gambio\StyleEdit\Adapters\ActiveThemeConfigurationAdapter;
use Gambio\StyleEdit\Adapters\ContentManagerAdapter;
use Gambio\StyleEdit\Adapters\Interfaces\CacheCleanerInterface;
use Gambio\StyleEdit\Adapters\Interfaces\ContentManagerAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\JwtAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\LanguageServiceAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\PagesAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\ProductSearchAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\ThemeActivatorAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\ThemeContentImporterAdapterInterface;
use Gambio\StyleEdit\Adapters\Interfaces\WidgetAdapterInterface;
use Gambio\StyleEdit\Adapters\JwtAdapter;
use Gambio\StyleEdit\Adapters\LanguageServiceAdapter;
use Gambio\StyleEdit\Adapters\PagesAdapter;
use Gambio\StyleEdit\Adapters\ProductSearchAdapter;
use Gambio\StyleEdit\Adapters\ShopCacheCleaner;
use Gambio\StyleEdit\Adapters\ShopThemeContentImporterAdapter;
use Gambio\StyleEdit\Adapters\ThemeActivatorAdapter;
use Gambio\StyleEdit\Adapters\WidgetAdapter;
use Gambio\StyleEdit\Api\Storage\StyleEditExpertModeStorage;
use Gambio\StyleEdit\Configurations\ShopBasePath;
use Gambio\StyleEdit\Configurations\ShopBaseUrl;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories\ContentManagerParserFactory;
use Gambio\StyleEdit\Core\Components\ContentManager\Parsers\Factories\Interfaces\ContentManagerParserFactoryInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\ActiveThemeInterface;
use Gambio\StyleEdit\Core\SingletonPrototype;
use GmConfigurationServiceInterface;
use GXModules\Gambio\StyleEdit\Adapters\CategorySearchAdapter;
use GXModules\Gambio\StyleEdit\Adapters\ConfigurationAdapter;
use GXModules\Gambio\StyleEdit\Adapters\CustomerServiceAdapter;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\CategorySearchAdapterInterface;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\ConfigurationAdapterInterface;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\CustomerServiceAdapterInterface;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\TextManagerAdapterInterface;
use GXModules\Gambio\StyleEdit\Adapters\TextManagerAdapter;
use InfoElementContentToThemeJsonConverter;
use InfoElementContentToThemeJsonConverterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use MainFactory;
use PagesLinkProvider;
use PagesNamespaceProvider;
use StaticGXCoreLoader;
use WidgetRegistrar;

/**
 * Class DependencyInjector
 *
 * @package Gambio\StyleEdit\Api
 * @codeCoverageIgnore
 */
class DependencyInjector
{


    protected static function bootstrapShop()
    {
        if(!class_exists(Application::class)) {
            if(isset($_SERVER['HTTP_X_THEME_ID'])){
                $_COOKIE['STYLE_EDIT_PREVIEW_THEME'] = $_SERVER['HTTP_X_THEME_ID'];
            }

            $includePath = get_include_path();

            set_include_path(dirname(__DIR__, 3) . '/');

            require_once 'GXMainComponents/ApplicationStyleEditApi.inc.php';

            $application = new ApplicationStyleEditApi();
            $application->run();


            set_include_path($includePath);
        }

    }

    /**
     * @throws \Exception
     */
    public static function inject(): void
    {


        static::bootstrapShop();
        /** @var StyleEditConfiguration $settings */
        $settings = SingletonPrototype::instance()->get(StyleEditConfiguration::class);

        SingletonPrototype::instance()->setUp(WidgetRegistrar::class,
            static function () {
                return MainFactory::create(WidgetRegistrar::class);
            });

        SingletonPrototype::instance()->setUp(\LanguageProviderInterface::class, static function () {
            return MainFactory::create(\LanguageProvider::class, StaticGXCoreLoader::getDatabaseQueryBuilder());
        });

        SingletonPrototype::instance()->setUp(ActiveThemeInterface::class,
            static function () {
                return ActiveThemeConfigurationAdapter::create();
            });

        SingletonPrototype::instance()->setUp(ThemeContentImporterAdapterInterface::class,
            static function () {
                return new ShopThemeContentImporterAdapter();
            });

        SingletonPrototype::instance()->setUp(ContentWriteServiceInterface::class,
            static function () {
                return \StaticGXCoreLoader::getService('ContentWrite');
            });
        SingletonPrototype::instance()->setUp(\ContentIdentificationFactoryInterface::class,
            static function () {
                return MainFactory::create(ContentIdentificationFactory::class);
            });

        SingletonPrototype::instance()->setUp(CacheCleanerInterface::class,
            static function () {
                return ShopCacheCleaner::create();
            });

        SingletonPrototype::instance()->setUp(StyleEditExpertModeStorage::class, static function () {
            return new StyleEditExpertModeStorage;
        });

        SingletonPrototype::instance()->setUp(ContentReadServiceInterface::class,
            static function () {
                return \StaticGXCoreLoader::getService('ContentRead');
            });

        SingletonPrototype::instance()->setUp(ContentDeleterInterface::class,
            static function () {
                return \StaticGXCoreLoader::getService('ContentDeleter');
            });
        
        SingletonPrototype::instance()->setUp(GmConfigurationServiceInterface::class,
            static function () {
                return \StaticGXCoreLoader::getService('GmConfiguration');
            });

        SingletonPrototype::instance()->setUp(LanguageServiceAdapterInterface::class,
            static function () {
                $provider = MainFactory::create(\LanguageProvider::class,
                    StaticGXCoreLoader::getDatabaseQueryBuilder());
                return new LanguageServiceAdapter($provider);
            });

        SingletonPrototype::instance()->setUp(ThemeActivatorAdapterInterface::class,
            static function () {
                return ThemeActivatorAdapter::create();
            });
        SingletonPrototype::instance()->setUp(ShopBaseUrl::class,
            static function () {
                return new ShopBaseUrl((ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG);
            });
        SingletonPrototype::instance()->setUp(ShopBasePath::class,
            static function () {
                return new ShopBasePath(DIR_FS_CATALOG);
            });
    
        SingletonPrototype::instance()->setUp(FilesystemAdapter::class,
            static function () use ($settings) {
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
            
                $filesystemAdapter = new LocalFilesystemAdapter($settings->themesFolderPath(),
                                                                $visibility,
                                                                LOCK_EX,
                                                                LocalFilesystemAdapter::DISALLOW_LINKS);
                $filesystem        = new Filesystem($filesystemAdapter);
            
                return MainFactory::create(FilesystemAdapter::class, $filesystem);
            });
    
        SingletonPrototype::instance()->setUp('FilesystemAdapterShopRoot',
            static function () {
                $permissionMap     = [
                    'file' => [
                        'public'  => 0777,
                        'private' => 0700,
                    ],
                    'dir'  => [
                        'public'  => 0777,
                        'private' => 0700,
                    ],
                ];
                $visibility        = PortableVisibilityConverter::fromArray($permissionMap);
                $filesystemAdapter = new LocalFilesystemAdapter(SHOP_ROOT,
                                                                $visibility,
                                                                LOCK_EX,
                                                                LocalFilesystemAdapter::DISALLOW_LINKS);
                $filesystem        = new Filesystem($filesystemAdapter);
            
                return MainFactory::create(FilesystemAdapter::class, $filesystem);
            });

        SingletonPrototype::instance()->setUp(InfoElementContentToThemeJsonConverterInterface::class,
            static function () {
                return MainFactory::create(InfoElementContentToThemeJsonConverter::class);
            });

        SingletonPrototype::instance()->setUp(ProductSearchAdapterInterface::class, static function() {
            return SingletonPrototype::instance()->get(ProductSearchAdapter::class);
        });
        SingletonPrototype::instance()->setUp(CategorySearchAdapterInterface::class, static function() {
            return SingletonPrototype::instance()->get(CategorySearchAdapter::class);
        });

        SingletonPrototype::instance()->setUp(JwtAdapterInterface::class, static function() {
            return new JwtAdapter();
        });
        
        SingletonPrototype::instance()->setUp(WidgetAdapterInterface::class, static function() {
            return new WidgetAdapter();
        });
        
        SingletonPrototype::instance()->setUp(PagesAdapterInterface::class, static function() {
            return SingletonPrototype::instance()->get(PagesAdapter::class);
        });
        
        SingletonPrototype::instance()->setUp(PagesNamespaceProvider::class, static function() {
            return MainFactory::create(PagesNamespaceProvider::class);
        });
        
        SingletonPrototype::instance()->setUp(CustomerServiceAdapterInterface::class, static function() {
            $customerServiceFactory = MainFactory::create(
                CustomerServiceFactory::class,
                StaticGXCoreLoader::getDatabaseQueryBuilder()
            );
            
            $customerReadService = $customerServiceFactory->createCustomerReadService();
        
            return SingletonPrototype::instance()->get(CustomerServiceAdapter::class, $customerReadService);
        });
    
        SingletonPrototype::instance()->setUp(ConfigurationAdapterInterface::class, static function() {
            $configurationService = \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
            return SingletonPrototype::instance()->get(ConfigurationAdapter::class, $configurationService);
        });
    
        SingletonPrototype::instance()->setUp(TextManagerAdapterInterface::class, static function() {
            $textManager = \LegacyDependencyContainer::getInstance()->get(TextManager::class);
            
            return SingletonPrototype::instance()->get(TextManagerAdapter::class, $textManager);
        });
    
        SingletonPrototype::instance()->setUp(PurposeDatabaseWriterInterface::class, static function() {
            return SingletonPrototype::instance()->get(
                PurposeDatabaseWriter::class,
                StaticGXCoreLoader::getDatabaseQueryBuilder(),
                MainFactory::create(\LanguageTextManager::class)
            );
        });
    
        SingletonPrototype::instance()->setUp(PurposeWriteRepositoryInterface::class, static function() {
            return SingletonPrototype::instance()->get(PurposeWriteRepository::class);
        });
    
        SingletonPrototype::instance()->setUp(CookieConsentPurposeReaderServiceInterface::class, static function() {
            $repository = SingletonPrototype::instance()->get(PurposeWriteRepositoryInterface::class);
            return SingletonPrototype::instance()->get(PurposeWriteService::class, $repository);
        });
        
        SingletonPrototype::instance()->setUp(ContentManagerAdapterInterface::class, static function () {
            return SingletonPrototype::instance()->get(ContentManagerAdapter::class);
        });
    
        SingletonPrototype::instance()->setUp(PagesLinkProvider::class, static function() {
            return MainFactory::create(PagesLinkProvider::class, MainFactory::create_object('GMSEOBoost', [], true));
        });
    
        SingletonPrototype::instance()->setUp(ContentManagerParserFactoryInterface::class, static function() {
            return SingletonPrototype::instance()->get(ContentManagerParserFactory::class);
        });
    
        SingletonPrototype::instance()->setUp(TextManager::class, static function() {
            return \LegacyDependencyContainer::getInstance()->get(TextManager::class);
        });
    
        SingletonPrototype::instance()->setUp(ConfigurationService::class, static function() {
            return \LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        });
    }
}
