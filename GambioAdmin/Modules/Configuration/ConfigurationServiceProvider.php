<?php
/* --------------------------------------------------------------
   ConfigurationServiceProvider.php 2021-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Configuration\App\Actions\ConfigurationOverview;
use Gambio\Admin\Modules\Configuration\App\Actions\FetchConfigurations;
use Gambio\Admin\Modules\Configuration\App\Actions\StoreConfiguration;
use Gambio\Admin\Modules\Configuration\App\Data\ConfigurationMapper;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\CategoryJsonReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\ConfigurationDbReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\ConfigurationJsonReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\GroupJsonReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\TagJsonReader;
use Gambio\Admin\Modules\Configuration\App\Data\Readers\TypeJsonReader;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\CategoryRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\Compatibility\ModuleCenterRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\ConfigurationRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\GroupRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\ListingCategoryRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\TagRepository;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\TypeRepository;
use Gambio\Admin\Modules\Configuration\Services\ConfigurationFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\CodFeeTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\ColorTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\CountryTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\CountryZoneTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\CurrencyTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\CustomerGroupTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\DateTimeTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\DateTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\DropdownTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\EmailTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\GeoZoneTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\LangTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\LanguageTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\MultiSelectTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\NumberTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\OrderStatusTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\PackageUnitTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\PasswordTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\PaymentMethodTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\SecondsTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\ShippingDestinationTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\ShippingMethodsTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\ShippingStatusTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\SliderTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\SwitcherTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\TaxClassTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\TaxCalculationModeTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\TextAreaTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\TextTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactories\WeightOrPriceTypeFactory;
use Gambio\Admin\Modules\Configuration\Services\TypeFactoryAggregation;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationService as CoreConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ConfigurationServiceProvider
 *
 * @package Gambio\Admin\Modules\Configuration
 */
class ConfigurationServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            // Compatibility components
            ModuleCenterRepository::class, // used in admin/modules.php
            
            ConfigurationOverview::class,
            FetchConfigurations::class,
            StoreConfiguration::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerTypeFactories();
        $this->registerRepositories();
        $this->registerReaders();
        $this->registerCompatibilityComponents();
        
        $this->application->registerShared(ConfigurationOverview::class);
        
        $this->application->registerShared(FetchConfigurations::class)
            ->addArgument(ListingCategoryRepository::class)
            ->addArgument(CategoryRepository::class)
            ->addArgument(TagRepository::class);
        
        $this->application->registerShared(StoreConfiguration::class)->addArgument(CoreConfigurationService::class);
        
        $this->application->registerShared(ConfigurationMapper::class)->addArgument(ConfigurationFactory::class);
        $this->application->registerShared(ConfigurationFactory::class)->addArgument(TypeFactoryAggregation::class);
    }
    
    
    private function registerTypeFactories(): void
    {
        $this->application->registerShared(ColorTypeFactory::class);
        $this->application->registerShared(DateTimeTypeFactory::class);
        $this->application->registerShared(DateTypeFactory::class);
        $this->application->registerShared(DropdownTypeFactory::class)->addArgument(TextManager::class);
        $this->application->registerShared(EmailTypeFactory::class);
        $this->application->registerShared(MultiSelectTypeFactory::class)->addArgument(TextManager::class);
        $this->application->registerShared(NumberTypeFactory::class);
        $this->application->registerShared(PasswordTypeFactory::class);
        $this->application->registerShared(SwitcherTypeFactory::class);
        $this->application->registerShared(TextAreaTypeFactory::class);
        $this->application->registerShared(TextTypeFactory::class);
        $this->application->registerShared(LangTypeFactory::class);
        $this->application->registerShared(SecondsTypeFactory::class);
        $this->application->registerShared(SliderTypeFactory::class);
        $this->application->registerShared(CountryTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(CountryZoneTypeFactory::class)->addArgument(Connection::class);
        $this->application->registerShared(OrderStatusTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        $this->application->registerShared(CustomerGroupTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        $this->application->registerShared(PackageUnitTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        $this->application->registerShared(ShippingStatusTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        $this->application->registerShared(CurrencyTypeFactory::class)->addArgument(Connection::class);
        $this->application->registerShared(TaxClassTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(GeoZoneTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(PaymentMethodTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(LanguageTypeFactory::class)->addArgument(LanguageService::class);
        $this->application->registerShared(ShippingMethodsTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(CodFeeTypeFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        $this->application->registerShared(TaxCalculationModeTypeFactory::class);
        $this->application->registerShared(ShippingDestinationTypeFactory::class);
        $this->application->registerShared(WeightOrPriceTypeFactory::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(TypeFactoryAggregation::class,
            function () {
                $factories = [
                    'color'                => $this->application->get(ColorTypeFactory::class),
                    'datetime'             => $this->application->get(DateTimeTypeFactory::class),
                    'date'                 => $this->application->get(DateTypeFactory::class),
                    'dropdown'             => $this->application->get(DropdownTypeFactory::class),
                    'email'                => $this->application->get(EmailTypeFactory::class),
                    'multi-select'         => $this->application->get(MultiSelectTypeFactory::class),
                    'number'               => $this->application->get(NumberTypeFactory::class),
                    'password'             => $this->application->get(PasswordTypeFactory::class),
                    'switcher'             => $this->application->get(SwitcherTypeFactory::class),
                    'textarea'             => $this->application->get(TextAreaTypeFactory::class),
                    'text'                 => $this->application->get(TextTypeFactory::class),
                    'lang'                 => $this->application->get(LangTypeFactory::class),
                    'country'              => $this->application->get(CountryTypeFactory::class),
                    'country-zone'         => $this->application->get(CountryZoneTypeFactory::class),
                    'order-status'         => $this->application->get(OrderStatusTypeFactory::class),
                    'currency'             => $this->application->get(CurrencyTypeFactory::class),
                    'customer-group'       => $this->application->get(CustomerGroupTypeFactory::class),
                    'package-unit'         => $this->application->get(PackageUnitTypeFactory::class),
                    'shipping-status'      => $this->application->get(ShippingStatusTypeFactory::class),
                    'language'             => $this->application->get(LanguageTypeFactory::class),
                    'tax-class'            => $this->application->get(TaxClassTypeFactory::class),
                    'geo-zone'             => $this->application->get(GeoZoneTypeFactory::class),
                    'payment-method'       => $this->application->get(PaymentMethodTypeFactory::class),
                    'seconds'              => $this->application->get(SecondsTypeFactory::class),
                    'slider'               => $this->application->get(SliderTypeFactory::class),
                    'shipping-method'      => $this->application->get(ShippingMethodsTypeFactory::class),
                    'cod-fee'              => $this->application->get(CodFeeTypeFactory::class),
                    'tax-calculation-mode' => $this->application->get(TaxCalculationModeTypeFactory::class),
                    'shipping-destination' => $this->application->get(ShippingDestinationTypeFactory::class),
                    'weight-or-price'      => $this->application->get(WeightOrPriceTypeFactory::class),
                ];
                
                return TypeFactoryAggregation::create($factories);
            });
    }
    
    
    private function registerRepositories(): void
    {
        $this->application->registerShared(ListingCategoryRepository::class)
            ->addArgument(CategoryRepository::class)
            ->addArgument(GroupRepository::class)
            ->addArgument(ConfigurationFactory::class);
        
        $this->application->registerShared(CategoryRepository::class)
            ->addArgument(CategoryJsonReader::class)
            ->addArgument(ConfigurationMapper::class)
            ->addArgument(ConfigurationFactory::class);
        
        $this->application->registerShared(GroupRepository::class)
            ->addArgument(GroupJsonReader::class)
            ->addArgument(ConfigurationRepository::class)
            ->addArgument(ConfigurationMapper::class)
            ->addArgument(ConfigurationFactory::class);
        
        $this->application->registerShared(ConfigurationRepository::class)
            ->addArgument(ConfigurationJsonReader::class)
            ->addArgument(ConfigurationDbReader::class)
            ->addArgument(TypeRepository::class)
            ->addArgument(ConfigurationMapper::class);
        
        $this->application->registerShared(TagRepository::class)
            ->addArgument(TagJsonReader::class)
            ->addArgument(ConfigurationMapper::class)
            ->addArgument(ConfigurationFactory::class);
        
        $this->application->registerShared(TypeRepository::class)
            ->addArgument(TypeJsonReader::class)
            ->addArgument(ConfigurationMapper::class);
    }
    
    
    private function registerReaders(): void
    {
        $this->application->registerShared(CategoryJsonReader::class)
            ->addArgument(__DIR__ . '/App/Data/definitions/categories.json')
            ->addArgument(__DIR__ . '/App/Data/definitions/custom.categories.json')
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(ConfigurationJsonReader::class)
            ->addArgument(__DIR__ . '/App/Data/definitions/configurations.json')
            ->addArgument(__DIR__ . '/App/Data/definitions/custom.configurations.json')
            ->addArgument(TagJsonReader::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(GroupJsonReader::class)
            ->addArgument(__DIR__ . '/App/Data/definitions/groups.json')
            ->addArgument(__DIR__ . '/App/Data/definitions/custom.groups.json')
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(TagJsonReader::class)
            ->addArgument(__DIR__ . '/App/Data/definitions/tags.json')
            ->addArgument(__DIR__ . '/App/Data/definitions/custom.tags.json')
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(TypeJsonReader::class)
            ->addArgument(__DIR__ . '/App/Data/definitions/types.json')
            ->addArgument(__DIR__ . '/App/Data/definitions/custom.types.json');
        
        $this->application->registerShared(ConfigurationDbReader::class)->addArgument(Connection::class);
    }
    
    
    public function registerCompatibilityComponents(): void
    {
        $this->application->registerShared(ModuleCenterRepository::class)->addArguments([
                                                                                            ConfigurationDbReader::class,
                                                                                            ConfigurationMapper::class,
                                                                                            TypeRepository::class
                                                                                        ]);
    }
}