<?php
/* --------------------------------------------------------------
  ProductListingShippingReader.php 2023-06-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Shipping;

use Doctrine\DBAL\Connection;

/**
 * Class ProductListingShippingReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Shipping
 */
class ProductListingShippingReader
{
    private const TABLE_NAME_SHIPPING = 'shipping_status';
    private const TABLE_ALIAS_SHIPPING = 'shipstat';
    private const TABLE_NAME_PRODUCTS = 'products';
    private const TABLE_ALIAS_PRODUCTS = 'p';
    private const TABLE_NAME_PRODUCTS_OPTIONS = 'products_properties_combis';
    private const TABLE_ALIAS_PRODUCTS_OPTIONS = 'ppc';
    private const COL_PID = 'products_id';
    private const COL_USE_OPTION_SHIPPING_TIME = self::TABLE_ALIAS_PRODUCTS . '.use_properties_combis_shipping_time';
    private const COL_LANGUAGE_ID = self::TABLE_ALIAS_SHIPPING . '.language_id';
    private const SHIPPING_COL_STATUS_ID = self::TABLE_ALIAS_SHIPPING . '.shipping_status_id';
    private const PRODUCTS_COL_SHIPPING_STATUS_ID = self::TABLE_ALIAS_PRODUCTS . '.products_shippingtime';
    private const SHIPPING_STATUS_SELECTS = [
        'days'         => self::TABLE_ALIAS_SHIPPING . '.number_of_days',
        'name'         => self::TABLE_ALIAS_SHIPPING . '.shipping_status_name',
        'image'        => self::TABLE_ALIAS_SHIPPING . '.shipping_status_image',
        'linkIsActive' => self::TABLE_ALIAS_SHIPPING . '.info_link_active',
    ];

    private Connection $connection;


    /**
     * ProductListingShippingReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Fetches shipping status information by product ID and language ID.
     *
     * Returns empty array if no shipping statuses (linked to this product ID/language ID) were found.
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return array
     */
    public function fetchStatus(int $productId, int $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();

        // prepare query params
        $tablesJoinConstraint = $qb->expr()->eq(self::PRODUCTS_COL_SHIPPING_STATUS_ID, self::SHIPPING_COL_STATUS_ID);
        $pIdEq                = $qb->expr()->eq(implode('.', [self::TABLE_ALIAS_PRODUCTS, self::COL_PID]),
                                                $qb->createNamedParameter($productId));
        $useShippingTime      = null;//$qb->expr()->eq(self::COL_USE_OPTION_SHIPPING_TIME, 1);
        $joinCondition        = $qb->expr()->and($tablesJoinConstraint, $pIdEq, $useShippingTime);
        $languageIdEq         = $qb->expr()->eq(self::COL_LANGUAGE_ID, $qb->createNamedParameter($languageId));
        $selects              = $this->getShippingStatusSelects();

        $statement = $qb->from(self::TABLE_NAME_SHIPPING, self::TABLE_ALIAS_SHIPPING)
            ->rightJoin(self::TABLE_ALIAS_SHIPPING,
                        self::TABLE_NAME_PRODUCTS,
                        self::TABLE_ALIAS_PRODUCTS,
                        $joinCondition)
            ->where($languageIdEq)
            ->select($selects)
            ->executeQuery();

        return $statement->fetchAssociative() ? : [];
    }


    /**
     * Fetches shipping status ranges of product options (properties) by product ID and language ID.
     *
     * @param int $productId
     * @param int $languageId
     *
     * @return array
     */
    public function fetchStatusRange(int $productId, int $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();

        // prepare query 1st rightJoin
        $productOptionsTablesJoinConstraint = $qb->expr()
            ->eq('ppc.combi_shipping_status_id', self::SHIPPING_COL_STATUS_ID);
        $pIdEq                              = $qb->expr()->eq('ppc.products_id', $qb->createNamedParameter($productId));
        $productOptionsJoinCondition        = $qb->expr()->and($productOptionsTablesJoinConstraint, $pIdEq);
        // prepare query 2nd rightJoin
        $productsTablesJoinConstraint = $qb->expr()->eq('ppc.products_id', 'p.products_id');
        $useShippingTime              = $qb->expr()->eq(self::COL_USE_OPTION_SHIPPING_TIME, 1);
        $productsJoinCondition        = $qb->expr()->and($productsTablesJoinConstraint, $useShippingTime);
        // prepare query other params
        $languageIdEq = $qb->expr()->eq(self::COL_LANGUAGE_ID, $qb->createNamedParameter($languageId));
        $groupBy      = array_values(self::SHIPPING_STATUS_SELECTS);
        $selects      = $this->getShippingStatusSelects();

        $statement = $qb->from(self::TABLE_NAME_SHIPPING, self::TABLE_ALIAS_SHIPPING)
            ->rightJoin(self::TABLE_ALIAS_SHIPPING,
                        self::TABLE_NAME_PRODUCTS_OPTIONS,
                        self::TABLE_ALIAS_PRODUCTS_OPTIONS,
                        $productOptionsJoinCondition)
            ->rightJoin(self::TABLE_ALIAS_PRODUCTS_OPTIONS,
                        self::TABLE_NAME_PRODUCTS,
                        self::TABLE_ALIAS_PRODUCTS,
                        $productsJoinCondition)
            ->where($languageIdEq)
            ->groupBy($groupBy)
            ->select($selects)
            ->executeQuery();

        return $statement->fetchAllAssociative();
    }


    /**
     * @return array
     */
    private function getShippingStatusSelects(): array
    {
        return array_map(function (string $name, string $alias) {
            return "{$name} AS {$alias}";
        },
            array_values(self::SHIPPING_STATUS_SELECTS),
            array_keys(self::SHIPPING_STATUS_SELECTS));
    }
}