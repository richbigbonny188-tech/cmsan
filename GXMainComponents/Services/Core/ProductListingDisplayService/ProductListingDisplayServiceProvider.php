<?php
/* --------------------------------------------------------------
  ProductListingDisplayServiceProvider.php 2023-05-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\Verification\Service\VerificationService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\AdaptersFactory;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayMapper;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayReader;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayRepository;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplaySettingsRepository;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayTextProvider;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayUrlsRepository;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\FiltersFactory;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ModelsFactory;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ModelsMapper;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingContextFilter;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingVerificationServiceInitializer;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\UrlBuilder;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\UrlSeoBoost;
use Gambio\Shop\Modules\ProductListing\App\Data\ProductListingModelFactory;

/**
 * Class ProductListingDisplayServiceProvider
 */
class ProductListingDisplayServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            DisplayService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        global $gmSEOBoost;
        
        $this->application->register(ProductListingVerificationServiceInitializer::class)
            ->addArgument(Environment::class)
            ->addArgument(CacheFactory::class)
            ->addArgument(VerificationService::class);
        $this->application->registerShared(ProductListingContextFilter::class);
        $this->application->registerShared(DisplayService::class)
            ->addArgument(DisplaySettingsRepository::class)
            ->addArgument(DisplayMapper::class);
        $this->application->register(DisplayMapper::class)->addArgument(DisplayRepository::class);
        $this->application->register(DisplayRepository::class)
            ->addArgument(DisplayReader::class)
            ->addArgument(DisplayTextProvider::class)
            ->addArgument(DisplayUrlsRepository::class)
            ->addArgument(ModelsMapper::class)
            ->addArgument(AdaptersFactory::class)
            ->addArgument(Path::class);
        $this->application->register(DisplaySettingsRepository::class)
            ->addArgument(DisplayReader::class)
            ->addArgument(ModelsMapper::class)
            ->addArgument(ModelsFactory::class);
        $this->application->register(DisplayReader::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(Connection::class);
        $this->application->register(DisplayTextProvider::class)->addArgument(TextManager::class);
        $this->application->register(DisplayUrlsRepository::class)
            ->addArgument(UrlBuilder::class)
            ->addArgument(UrlSeoBoost::class);
        $this->application->register(UrlBuilder::class)->addArgument(Url::class);
        $this->application->register(UrlSeoBoost::class)->addArgument($gmSEOBoost);
        $this->application->register(ModelsMapper::class)->addArgument(ModelsFactory::class);
        $this->application->register(FiltersFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(ProductListingModelFactory::class);
        $this->application->register(ModelsFactory::class);
        $this->application->register(AdaptersFactory::class);
    }
}