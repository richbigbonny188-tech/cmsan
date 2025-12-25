<?php
/* --------------------------------------------------------------
  ProductListingDisplayServiceFactory.php 2023-05-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Core\Verification\Service\VerificationService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\DisplayService;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\FiltersFactory;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingContextFilter;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data\ProductListingVerificationServiceInitializer;
use Gambio\MainComponents\Services\Core\ProductListingDisplayService\Model\ValueObjects\ProductListingControl;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Service\ListingService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ProductListingDisplayServiceFactory
 */
class ProductListingDisplayServiceFactory
{
    private const DEFAULTS_LISTING_PAGINATION_PAGE     = 1;
    private const DEFAULTS_LISTING_PAGINATION_PER_PAGE = 20;


    /**
     * @return ProductListingDisplayService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(): ProductListingDisplayService
    {
        $container = LegacyDependencyContainer::getInstance();

        return new ProductListingDisplayService(
            $container->get(ListingService::class),
            $container->get(DisplayService::class),
            $container->get(FiltersFactory::class),
            $this->createProductListingControl(),
            $container->get(ProductListingVerificationServiceInitializer::class),
            $container->get(ProductListingContextFilter::class)
        );
    }


    /**
     * @return ProductListingControl
     */
    private function createProductListingControl(): ProductListingControl
    {
        return new ProductListingControl(
            new ListingSettings(
                (int)$_SESSION['languages_id'],
                !isset($_SESSION['customer_id']) ? null : (int)$_SESSION['customer_id'],
                $_SESSION['currency']
            ),
            new ListingPagination(
                self::DEFAULTS_LISTING_PAGINATION_PAGE,
                self::DEFAULTS_LISTING_PAGINATION_PER_PAGE
            )
        );
    }
}