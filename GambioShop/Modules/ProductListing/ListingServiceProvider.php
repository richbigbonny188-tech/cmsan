<?php
/*
 * --------------------------------------------------------------
 *   ListingServiceProvider.php 2023-12-04
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Shop\Modules\ProductListing\App\Data\Currency\ProductListingCurrencyRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Downloads\ProductListingDownloadRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\GroupSettings\ProductListingGroupSettingsReader;
use Gambio\Shop\Modules\ProductListing\App\Data\GroupSettings\ProductListingGroupSettingsRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Images\ProductListingImagesModelsFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Images\ProductListingImagesReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Images\ProductListingImagesRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceCalculationHelper;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceCalculator;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceDiscountRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFormatter;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceFormatterUtility;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceOptionReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPricePersonalOfferRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceSpecialRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceStockSettingsReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceStockSettingsRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextFormatter;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceTextProvider;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantCheckReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ProductListingPriceVariantStockCheckReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPricePersonalOfferHandlerHelper;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPriceSpecialHandlerHelper;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Helper\ProductListingPriceVariantsHandlerHelper;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceDiscountHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPricePersonalOfferHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceSpecialHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceStatusHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\ProductListingPriceVariantsHandler;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingMapper;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Shipping\ProductListingShippingModelFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Shipping\ProductListingShippingReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Shipping\ProductListingShippingRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxCalculator;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxFormatPhrase;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxFormatSettings;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxFormatter;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxFormatTranslationFactory;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxReader;
use Gambio\Shop\Modules\ProductListing\App\Data\Tax\ProductListingTaxRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Vpe\ProductListingVpeCalculator;
use Gambio\Shop\Modules\ProductListing\App\Data\Vpe\ProductListingVpeNumberFormatter;
use Gambio\Shop\Modules\ProductListing\App\Data\Vpe\ProductListingVpeRepository;
use Gambio\Shop\Modules\ProductListing\App\Data\Vpe\ProductListingVpeTextProvider;
use Gambio\Shop\Modules\ProductListing\App\ProductListingLegacyService;
use Gambio\Shop\Modules\ProductListing\ListingDisplay\AdaptersServiceProvider;
use Gambio\Shop\Modules\ProductListing\ListingDisplay\DisplayService;
use Gambio\Shop\Modules\ProductListing\Service\ListingRepository;
use Gambio\Shop\Modules\ProductListing\Service\ListingService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ListingServiceProvider
 *
 * @package Gambio\Shop\Modules\ProductListing
 */
class ListingServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ListingService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ListingService::class, App\ProductListingService::class)
            ->addArgument(EventDispatcherInterface::class)
            ->addArgument(ListingRepository::class);
        
        $this->application->registerShared(ProductListingModelFactory::class);
        $this->application->registerShared(ProductListingCurrencyRepository::class)->addArgument(Connection::class);
        
        $this->registerRepository();
        $this->registerGroupSettings();
        $this->registerImageComponents();
        $this->registerTaxComponents();
        $this->registerPriceComponents();
        $this->registerShippingComponents();
        $this->registerVpeComponents();
    }
    
    
    /**
     * Registers the repository and their components.
     */
    private function registerRepository(): void
    {
        $this->application->registerShared(ProductListingDownloadRepository::class)->addArgument(Connection::class);
        $this->application->registerShared(ListingRepository::class, App\Data\ProductListingRepository::class)
            ->addArgument(ProductListingReader::class)
            ->addArgument(ProductListingMapper::class)
            ->addArgument(ProductListingModelFactory::class);
        $this->application->registerShared(ProductListingReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductListingMapper::class)
            ->addArgument(ProductListingModelFactory::class)
            ->addArgument(ProductListingShippingRepository::class)
            ->addArgument(ProductListingTaxRepository::class)
            ->addArgument(ProductListingPriceRepository::class)
            ->addArgument(ProductListingImagesRepository::class)
            ->addArgument(ProductListingGroupSettingsRepository::class)
            ->addArgument(ProductListingCurrencyRepository::class)
            ->addArgument(ProductListingDownloadRepository::class)
            ->addArgument(ProductListingVpeRepository::class);
    }
    
    
    /**
     * Registers group settings repository.
     */
    private function registerGroupSettings(): void
    {
        $this->application->registerShared(ProductListingGroupSettingsRepository::class)
            ->addArgument(ProductListingGroupSettingsReader::class);
        $this->application->registerShared(ProductListingGroupSettingsReader::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class);
    }
    
    
    /**
     * Registers all tax related components.
     */
    private function registerTaxComponents(): void
    {
        $this->application->registerShared(ProductListingTaxRepository::class)
            ->addArgument(ProductListingTaxReader::class)
            ->addArgument(ProductListingTaxCalculator::class)
            ->addArgument(ProductListingTaxFormatter::class)
            ->addArgument(ProductListingModelFactory::class);
        
        $this->application->registerShared(ProductListingTaxReader::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class);
        $this->application->registerShared(ProductListingTaxCalculator::class);
        $this->application->registerShared(ProductListingTaxFormatter::class)
            ->addArgument(TextManager::class)
            ->addArgument(ProductListingTaxFormatPhrase::class);
        $this->application->registerShared(ProductListingTaxFormatPhrase::class)
            ->addArgument(ProductListingTaxFormatTranslationFactory::class);
        $this->application->registerShared(ProductListingTaxFormatTranslationFactory::class)
            ->addArgument(ProductListingTaxFormatSettings::class);
        $this->application->registerShared(ProductListingTaxFormatSettings::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class);
    }
    
    
    /**
     * Registers all components related to listing item shipping information.
     */
    private function registerShippingComponents(): void
    {
        $this->application->registerShared(ProductListingShippingRepository::class)
            ->addArgument(ProductListingShippingReader::class)
            ->addArgument(ProductListingShippingModelFactory::class);
        $this->application->registerShared(ProductListingShippingReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductListingShippingModelFactory::class);
    }
    
    
    /**
     * Registers all components for the image repository.
     */
    private function registerImageComponents(): void
    {
        $this->application->registerShared(ProductListingImagesRepository::class)
            ->addArgument(ProductListingImagesReader::class)
            ->addArgument(ProductListingImagesModelsFactory::class);
        
        $this->application->registerShared(ProductListingImagesReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductListingImagesModelsFactory::class);
    }
    
    
    /**
     * Registers all components related to listing item price calculation.
     */
    public function registerPriceComponents(): void
    {
        $this->application->registerShared(ProductListingPriceRepository::class)
            ->addArgument(ProductListingPriceHandler::class)
            ->addArgument(ProductListingPriceStatusHandler::class)
            ->addArgument(ProductListingPriceSpecialHandler::class)
            ->addArgument(ProductListingPriceVariantsHandler::class)
            ->addArgument(ProductListingPricePersonalOfferHandler::class)
            ->addArgument(ProductListingPriceDiscountHandler::class)
            ->addArgument(ProductListingPriceTextProvider::class)
            ->addArgument(ProductListingPriceFormatter::class)
            ->addArgument(ProductListingPriceFactory::class);
        
        $this->application->registerShared(ProductListingPriceHandler::class)
            ->addArgument(ProductListingPriceCalculator::class)
            ->addArgument(ProductListingPriceFactory::class)
            ->addArgument(ProductListingPriceFormatter::class);
        
        $this->application->registerShared(ProductListingPriceFactory::class);
        $this->application->registerShared(ProductListingPriceTextProvider::class)->addArgument(TextManager::class);
        $this->application->registerShared(ProductListingPriceTextFormatter::class)
            ->addArgument(ProductListingPriceTextProvider::class)
            ->addArgument(ProductListingPriceFormatter::class);
        $this->application->registerShared(ProductListingPriceFormatter::class)
            ->addArgument(ProductListingPriceFormatterUtility::class);
        $this->application->registerShared(ProductListingPriceFormatterUtility::class);
        
        $this->registerPriceStatusHandler();
        $this->registerPriceSpecialHandler();
        $this->registerVariantsPriceSpecialHandler();
        $this->registerPricePersonalOfferHandler();
        $this->registerPriceDiscountHandler();
        $this->registerPriceVariantRepository();
        $this->registerPriceCalculationHelperComponents();
    }
    
    
    /**
     * Registers the price status handler.
     */
    private function registerPriceStatusHandler(): void
    {
        $this->application->registerShared(ProductListingPriceStatusHandler::class)
            ->addArgument(ProductListingPriceTextProvider::class);
    }
    
    
    /**
     * Registers the price discount handler.
     */
    private function registerPriceDiscountHandler(): void
    {
        $this->application->registerShared(ProductListingPriceDiscountHandler::class)
            ->addArgument(ProductListingPriceDiscountRepository::class)
            ->addArgument(ProductListingPriceVariantRepository::class)
            ->addArgument(ProductListingPriceFactory::class)
            ->addArgument(ProductListingPriceTextFormatter::class);
        $this->application->registerShared(ProductListingPriceDiscountRepository::class)
            ->addArgument(Connection::class);
    }
    
    
    /**
     * Registers the price personal offer handler.
     */
    private function registerPricePersonalOfferHandler(): void
    {
        $this->application->registerShared(ProductListingPricePersonalOfferHandler::class)
            ->addArgument(ProductListingPricePersonalOfferRepository::class)
            ->addArgument(ProductListingPriceDiscountHandler::class)
            ->addArgument(ProductListingPricePersonalOfferHandlerHelper::class)
            ->addArgument(ProductListingPriceFactory::class);
        $this->application->registerShared(ProductListingPricePersonalOfferRepository::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(ProductListingPricePersonalOfferHandlerHelper::class)
            ->addArgument(ProductListingPriceCalculationHelper::class)
            ->addArgument(ProductListingPriceFactory::class)
            ->addArgument(ProductListingPriceTextFormatter::class);
    }
    
    
    /**
     * Registers the special price handler.
     */
    private function registerPriceSpecialHandler(): void
    {
        $this->application->registerShared(ProductListingPriceSpecialHandler::class)
            ->addArgument(ProductListingPriceSpecialHandlerHelper::class)
            ->addArgument(ProductListingPriceSpecialRepository::class)
            ->addArgument(ProductListingPriceFactory::class);
        $this->application->registerShared(ProductListingPriceSpecialHandlerHelper::class)
            ->addArgument(ProductListingPriceCalculationHelper::class)
            ->addArgument(ProductListingPriceFactory::class)
            ->addArgument(ProductListingPriceTextFormatter::class);
        $this->application->registerShared(ProductListingPriceSpecialRepository::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class);
    }
    
    
    /**
     * Registers the special price handler.
     */
    private function registerVariantsPriceSpecialHandler(): void
    {
        $this->application->registerShared(ProductListingPriceVariantsHandler::class)
            ->addArgument(ProductListingPriceVariantsHandlerHelper::class)
            ->addArgument(ProductListingPriceVariantRepository::class)
            ->addArgument(ProductListingPriceFactory::class);
        $this->application->registerShared(ProductListingPriceVariantsHandlerHelper::class)
            ->addArgument(ProductListingPriceCalculationHelper::class)
            ->addArgument(ProductListingPriceFactory::class)
            ->addArgument(ProductListingPriceTextFormatter::class)
            ->addArgument(ProductListingPriceSpecialRepository::class);
    }
    
    
    /**
     * Registers price calculation and calculation helper components.
     */
    private function registerPriceCalculationHelperComponents(): void
    {
        $this->application->registerShared(ProductListingPriceCalculationHelper::class)
            ->addArgument(ProductListingPriceCalculator::class);
        $this->application->registerShared(ProductListingPriceCalculator::class)
            ->addArgument(ProductListingPriceVariantRepository::class);
    }
    
    
    /**
     * Registers the variant price repository which provides an API for product variant
     * and product option related prices of a listing item.
     */
    private function registerPriceVariantRepository(): void
    {
        $this->application->registerShared(ProductListingPriceVariantRepository::class)
            ->addArgument(ProductListingPriceVariantReader::class)
            ->addArgument(ProductListingPriceOptionReader::class)
            ->addArgument(ProductListingPriceVariantStockCheckReader::class)
            ->addArgument(ProductListingPriceStockSettingsRepository::class)
            ->addArgument(ProductListingPriceVariantCheckReader::class)
            ->addArgument(ProductListingPriceSpecialRepository::class);
        $this->application->registerShared(ProductListingPriceVariantReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductListingPriceOptionReader::class)->addArgument(Connection::class);
        $this->application->registerShared(ProductListingPriceVariantStockCheckReader::class)
            ->addArgument(Connection::class);
        $this->application->registerShared(ProductListingPriceStockSettingsRepository::class)
            ->addArgument(ProductListingPriceStockSettingsReader::class);
        $this->application->registerShared(ProductListingPriceStockSettingsReader::class)
            ->addArgument(ConfigurationFinder::class);
        $this->application->registerShared(ProductListingPriceVariantCheckReader::class)
            ->addArgument(Connection::class);
    }
    
    
    private function registerVpeComponents(): void
    {
        $this->application->registerShared(ProductListingVpeCalculator::class);
        $this->application->registerShared(ProductListingVpeNumberFormatter::class);
        $this->application->registerShared(ProductListingVpeTextProvider::class)->addArgument(TextManager::class);
        $this->application->registerShared(ProductListingVpeRepository::class)
            ->addArgument(ProductListingVpeCalculator::class)
            ->addArgument(ProductListingVpeNumberFormatter::class)
            ->addArgument(ProductListingVpeTextProvider::class);
    }
}