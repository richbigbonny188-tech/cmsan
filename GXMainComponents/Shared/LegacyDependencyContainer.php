<?php
/*------------------------------------------------------------------------------
 LegacyDependencyContainer.php 2024-04-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2024 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Admin\Layout\Menu\AdminMenuServiceProvider;
use Gambio\Admin\Modules\Configuration\ConfigurationServiceProvider as AdminConfigurationServiceProvider;
use Gambio\Admin\Modules\ImageList\ImageListServiceProvider;
use Gambio\Admin\Modules\Option\OptionServiceProvider;
use Gambio\Admin\Modules\ParcelService\ParcelServiceServiceProvider;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\AdditionalOptionServiceProvider;
use Gambio\Admin\Modules\ProductOption\ProductOptionServiceProvider as DeprecatedProductOptionServiceProvider;
use Gambio\Admin\Modules\Product\Submodules\Variant\ProductVariantServiceProvider;
use Gambio\Admin\Modules\ProductVariant\ProductVariantServiceProvider as DeprecatedProductVariantServiceProvider;
use Gambio\Admin\Modules\RedirectRules\RedirectRulesServiceProvider;
use Gambio\Admin\Modules\TrackingCode\TrackingCodeServiceProvider;
use Gambio\Admin\Modules\UserFriendlyErrorPage\UserFriendlyErrorPageServiceProvider;
use Gambio\Admin\Modules\Withdrawal\WithdrawalServiceProvider;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper\LoadUserPreferencesFromSession;
use Gambio\Core\Application\DependencyInjection\Abstraction\LeagueContainer;
use Gambio\Core\Application\ServiceProviders\CiDbServiceProvider;
use Gambio\Core\Application\ServiceProviders\DoctrineQbServiceProvider;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\ShopInformation;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Auth\AuthenticationServiceProvider;
use Gambio\Core\Cache\CacheServiceProvider;
use Gambio\Core\Configuration\ConfigurationServiceProvider as CoreConfigurationServiceProvider;
use Gambio\Core\ErrorHandling\ErrorHandlingServiceProvider;
use Gambio\Core\Event\EventDispatcherServiceProvider;
use Gambio\Core\Filesystem\FilesystemServiceProvider;
use Gambio\Core\GXModules\GXModulesServiceProvider;
use Gambio\Core\Images\ImagesServiceProvider;
use Gambio\Core\Language\LanguageServiceProvider;
use Gambio\Core\Logging\LoggingServiceProvider;
use Gambio\Core\Permission\PermissionServiceProvider;
use Gambio\Core\TemplateEngine\TemplateEngineServiceProvider;
use Gambio\Core\TextManager\TextManagerServiceProvider;
use Gambio\Core\Verification\VerificationServiceProvider;
use Gambio\Shop\Attributes\SellingUnitPrice\ServiceProvider as PriceAttributeInformationServiceProvider;
use Gambio\Shop\Product\AvailabilityDate\ServiceProvider as ProductAvailabilityDateServiceProvider;
use Gambio\Shop\Product\Description\ServiceProvider as ProductDescriptionServiceProvider;
use Gambio\Shop\Product\Ean\ServiceProvider as ProductEanServiceProvider;
use Gambio\Shop\Product\LegalAgeFlag\ServiceProvider as ProductLegalAgeFlagServiceProvider;
use Gambio\Shop\Product\Name\ServiceProvider as ProductNameServiceProvider;
use Gambio\Shop\Product\NumberOfOrders\ServiceProvider as ProductNumberOfOrdersServiceProvider;
use Gambio\Shop\Product\ReleaseDate\ServiceProvider as ProductReleaseDateServiceProvider;
use Gambio\Shop\Product\SellingUnitImage\Database\ServiceProvider;
use Gambio\Shop\Product\Status\ServiceProvider as ProductStatusServiceProvider;
use Gambio\Shop\Product\Tabs\ServiceProvider as ProductTabsServiceProvider;
use Gambio\Shop\Product\Url\ServiceProvider as ProductUrlServiceProvider;
use Gambio\Shop\ProductModifiers\ProductModifiersServiceProvider;
use Gambio\Shop\SellingUnit\Database\Price\ProductInformation\ServiceProvider as PriceProductInformationServiceProvider;
use Gambio\Shop\SellingUnit\Database\Unit\SellingUnitServiceProvider;
use Gambio\Shop\UserNavigationHistory\UserNavigationHistoryServiceProvider;
use Gambio\Shop\SampleData\ServiceProvider as SampleDataServiceProvider;
use Gambio\Testing\Framework\DoctrineQbTestServiceProvider;
use GXModules\Gambio\Afterbuy\Admin\Module\AfterbuyServiceProvider;
use GXModules\Magnalister\Magnalister\MagnalisterServiceProvider;

/**
 * Class LegacyDependencyContainer
 */
class LegacyDependencyContainer
{
    /**
     * @var Application
     */
    protected static $application;
    
    /**
     * @var LeagueContainer
     */
    protected static $container;
    
    
    /**
     * @return Application
     */
    public static function getInstance(): Application
    {
        if (!self::$application instanceof Application) {
            if (!class_exists(Application::class)) {
                require_once __DIR__ . '/../../vendor/autoload.php';
            }
            
            self::$container   = LeagueContainer::create();
            self::$application = new Application(self::$container);
            $isDev             = file_exists(__DIR__ . '/../../.dev-environment');
            $isEndToEnd        = file_exists(__DIR__ . '/../../.e2e');
            $isCloud           = file_exists(__DIR__ . '/../../version_info/cloud.php');
            $hasContract       = file_exists(__DIR__ . '/../../debug/.contract');
            
            if (defined('UNIT_TEST_RUNNING')) {
                $serverPath = '/var/www/html';
                $host       = 'www.mein-test-shop.de';
                $webPath    = '';
                $sslEnabled = true;
                $requestUri = 'www.mein-test-shop.de';
                
                self::$application->registerShared(Path::class)->addArgument($serverPath);
                self::$application->registerShared(Url::class)->addArguments([$host, $webPath]);
                self::$application->registerShared(Server::class)->addArguments([$sslEnabled, $requestUri]);
                self::$application->registerProvider(DoctrineQbTestServiceProvider::class);
                self::$application->registerShared(Environment::class)->addArgument(true)->addArgument($isCloud)->addArgument(false);
                self::$application->registerShared(ShopInformation::class)->addArgument($isCloud)->addArgument($hasContract);
            } else {
                $host       = HTTP_SERVER;
                $webPath    = rtrim(DIR_WS_CATALOG, '/');
                $serverPath = rtrim(DIR_FS_CATALOG, '/');
                
                $isSslEnabled = false;
                if (defined('ENABLE_SSL')) {
                    $isSslEnabled = ENABLE_SSL === true;
                } elseif (defined('ENABLE_SSL_CATALOG')) {
                    $isSslEnabled = strtolower(ENABLE_SSL_CATALOG) === 'true';
                }
                
                $requestUri = $_SERVER['REQUEST_URI'];
                
                self::$application->registerShared(Path::class)->addArgument($serverPath);
                self::$application->registerShared(Url::class)->addArguments([$host, $webPath]);
                self::$application->registerShared(Server::class)->addArguments([$isSslEnabled, $requestUri]);
                self::$application->registerProvider(DoctrineQbServiceProvider::class);
                self::$application->registerShared(Environment::class)->addArgument($isDev)->addArgument($isCloud)->addArgument($isEndToEnd);
                self::$application->registerShared(ShopInformation::class)->addArgument($isCloud)->addArgument($hasContract);
            }
            
            self::$application->registerProvider(LoggingServiceProvider::class);
            self::$application->registerProvider(TextManagerServiceProvider::class);
            self::$application->registerProvider(CacheServiceProvider::class);
            self::$application->registerProvider(EventDispatcherServiceProvider::class);
            self::$application->registerProvider(FilesystemServiceProvider::class);
            self::$application->registerProvider(ImagesServiceProvider::class);
            self::$application->registerProvider(AuthenticationServiceProvider::class);
            self::$application->registerProvider(CoreConfigurationServiceProvider::class);
            self::$application->registerProvider(ParcelServiceServiceProvider::class);
            self::$application->registerProvider(RedirectRulesServiceProvider::class);
            self::$application->registerProvider(TrackingCodeServiceProvider::class);
            self::$application->registerProvider(LanguageServiceProvider::class);
            self::$application->registerProvider(WithdrawalServiceProvider::class);
            self::$application->registerProvider(OptionServiceProvider::class);
            self::$application->registerProvider(ProductVariantServiceProvider::class);
            self::$application->registerProvider(DeprecatedProductVariantServiceProvider::class);
            self::$application->registerProvider(AdditionalOptionServiceProvider::class);
            self::$application->registerProvider(DeprecatedProductOptionServiceProvider::class);
            self::$application->registerProvider(ImageListServiceProvider::class);
            self::$application->registerProvider(PermissionServiceProvider::class);
            self::$application->registerProvider(TemplateEngineServiceProvider::class);
            self::$application->registerProvider(GXModulesServiceProvider::class);
            self::$application->registerProvider(AdminMenuServiceProvider::class);
            self::$application->registerProvider(AdminConfigurationServiceProvider::class);
            self::$application->registerProvider(UserFriendlyErrorPageServiceProvider::class);
            self::$application->registerProvider(ErrorHandlingServiceProvider::class);
            self::$application->registerProvider(SampleDataServiceProvider::class);
            
            self::$application->registerProvider(CiDbServiceProvider::class);
            
            self::$application->registerProvider(ServiceProvider::class);
            
            /**
             * @internal Do not change the order for OnCreateSellingUnitEvent listeners
             */
            self::$application->registerProvider(\Gambio\Shop\Price\Product\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\Product\Database\ServiceProvider::class);
            self::$application->registerProvider(SellingUnitServiceProvider::class);
            self::$application->registerProvider(PriceProductInformationServiceProvider::class);
            self::$application->registerProvider(PriceAttributeInformationServiceProvider::class);
            
            self::$application->registerProvider(ProductNameServiceProvider::class);
            self::$application->registerProvider(ProductUrlServiceProvider::class);
            self::$application->registerProvider(ProductTabsServiceProvider::class);
            self::$application->registerProvider(ProductNumberOfOrdersServiceProvider::class);
            self::$application->registerProvider(ProductDescriptionServiceProvider::class);
            self::$application->registerProvider(ProductLegalAgeFlagServiceProvider::class);
            self::$application->registerProvider(ProductAvailabilityDateServiceProvider::class);
            self::$application->registerProvider(ProductReleaseDateServiceProvider::class);
            self::$application->registerProvider(ProductStatusServiceProvider::class);
            self::$application->registerProvider(ProductEanServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Properties\SellingUnitImages\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\SellingUnitImages\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\ProductModifiers\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Properties\ProductModifiers\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\SellingUnitQuantitiy\Database\ServiceProvider::class);
            
            self::$application->registerProvider(\Gambio\Shop\Properties\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\SellingUnitQuantitiy\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Properties\SellingUnit\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\SellingUnit\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\SellingUnit\Unit\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\SellingUnit\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\GxCustomizer\SellingUnit\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\Model\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\SellingUnitModel\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\Weight\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\SellingUnitWeight\Database\ServiceProvider::class);
            // DO NOT CHANGE: EAN-Attributes/Properties providers' order unless you want attributes to override properties
            self::$application->registerProvider(\Gambio\Shop\Product\ShippingLink\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\TaxInfo\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\SellingUnit\Presentation\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Attributes\Representation\Id\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Properties\Representation\Id\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\Representation\ProductLink\ServiceProvider::class);
            self::$application->registerProvider(Gambio\Shop\Attributes\Representation\SelectionHtml\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Properties\Representation\SelectionHtml\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\Representation\ShortDescription\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\SellingUnitVpe\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Stock\SellingUnitStock\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\SellingUnit\Database\Image\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\GxCustomizer\Representation\Id\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\GxCustomizer\ProductModifiers\Database\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\AdditionalPriceInformation\ServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Product\DiscountAllowed\ServiceProvider::class);
            self::$application->registerProvider(ProductModifiersServiceProvider::class);
            self::$application->registerProvider(UserNavigationHistoryServiceProvider::class);
            self::$application->registerProvider(\Gambio\Admin\Modules\Customer\CustomerServiceProvider::class);
            self::$application->registerProvider(VerificationServiceProvider::class);
            self::$application->registerProvider(\Gambio\Shop\Modules\ProductListing\ListingServiceProvider::class);
            self::$application->registerProvider(\Gambio\MainComponents\Services\Core\ProductListingDisplayService\ProductListingDisplayServiceProvider::class);
            self::$application->registerProvider(\Gambio\Core\VatValidation\VatValidationServiceProvider::class);

            /**
             * @internal This service provider is currently included manually and from time to time we should check,
             *           if this is still needed. TODO: Check if this is still needed :D
             */
            self::$application->registerProvider(MagnalisterServiceProvider::class);
            
            if (!defined('UNIT_TEST_RUNNING')) {
                // LoadSessionData have to be booted after the configuration repository was registered
                $loadSessionData = new LoadUserPreferencesFromSession();
                $loadSessionData->boot(self::$application);
            }
            
            /**
             * Custom registration of GXModules service providers
             */
            self::$application->registerProvider(AfterbuyServiceProvider::class);
        }
        
        return self::$application;
    }
}
