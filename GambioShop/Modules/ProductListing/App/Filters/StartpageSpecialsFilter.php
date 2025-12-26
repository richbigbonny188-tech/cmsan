<?php
/*
 * --------------------------------------------------------------
 *   StartpageSpecialsFilter.php 2023-06-14
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Event\SpecialsListingCollected;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\Listing;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingItemId;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortDirection;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortValue;
use Gambio\Shop\Modules\ProductListing\Service\ListingFilter;

/**
 * Class StartpageSpecialsFilter
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
class StartpageSpecialsFilter implements ListingFilter
{
    use FilterUtilityTrait;

    private ListingSettings $settings;
    private Connection $connection;
    private ConfigurationFinder $configurationFinder;


    public function __construct(
        ListingSettings     $settings,
        Connection          $connection,
        ConfigurationFinder $configurationFinder
    )
    {
        $this->settings            = $settings;
        $this->connection          = $connection;
        $this->configurationFinder = $configurationFinder;
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getProductIds(): ListingItemIds
    {
        $cb = static function (array $item): ListingItemId {
            return new ListingItemId((int)$item['products_id']);
        };

        $result = $this->buildStatement();

        return new ListingItemIds(...array_map($cb, $result->fetchAllAssociative()));
    }


    /**
     * @inheritDoc
     */
    public function getSortOrder(): ListingSortOrder
    {
        return new ListingSortOrder(ListingSortValue::startPage(), ListingSortDirection::asc());
    }


    /**
     * @inheritDoc
     */
    public function getSettings(): ListingSettings
    {
        return $this->settings;
    }


    /**
     * @inheritDoc
     */
    public function getListingEvent(Listing $listing): ?object
    {
        return new SpecialsListingCollected($listing);
    }


    /**
     * Builds a sql statement fetching ids for products that should appear on the start page.
     * This method also takes group and fsk checks into account.
     *
     * @return Result
     * @throws Exception
     * @see \SpecialsMainThemeContentView::build_sql_query()
     *
     */
    private function buildStatement(): Result
    {
        $qb        = $this->connection->createQueryBuilder();
        $subSelect = $this->connection->createQueryBuilder()
                                      ->select('s.products_id')
                                      ->from('specials', 's')
                                      ->where($qb->expr()->eq('s.status', '1'));

        $qb->select('p.products_id')
           ->from(sprintf('(%s)', $subSelect->getSQL()), 's')
           ->from('products', 'p')
           ->where($qb->expr()->eq('p.products_id', 's.products_id'));

        $qb = $this->prepareDefaultStatement($qb, $this->settings, $this->configurationFinder, $this->connection);

        // @todo: this should be uncommented to differentiate the specials on startpage from other specials filters
        // $qb = $this->andWhereIsStartpage($qb);

        return $qb->executeQuery();
    }
}