<?php
/* --------------------------------------------------------------
   ProductListingReader.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Shop\Modules\ProductListing\Model\Collections\ListingItemIds;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingPagination;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSortOrder;
use UnexpectedValueException;

/**
 * Class ProductListingReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingReader
{
    private Connection $connection;
    
    
    /**
     * ProductListingReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * Fetches product data as raw array from the database.
     *
     * @param ListingItemIds        $ids
     * @param ListingPagination     $pagination
     * @param ListingSortOrder|null $sortOrder
     * @param ListingSettings       $settings
     *
     * @return array
     */
    public function fetch(
        ListingItemIds    $ids,
        ListingPagination $pagination,
        ?ListingSortOrder  $sortOrder,
        ListingSettings   $settings
    ): array {
        $qb          = $this->connection->createQueryBuilder();
        $selectArray = [
            'p.products_id as id',
            'p.products_price as price',
            'pd.products_name as name',
            'pd.products_description as description',
            'pd.products_short_description as shortDescription',
            'p.products_model as model',
            'pic.code_isbn as isbn',
            'pic.code_upc as upc',
            'pic.code_mpn as mpn',
            'pic.code_jan as jan',
            'p.gm_priority as metaSitemapPriority',
            'p.gm_changefreq as metaSitemapFrequency',
            'ptd.name as `type`',
            'p.products_weight as weight',
            'p.products_fsk18 as fsk18',
            'p.products_vpe_value as vpeValue',
            'pv.products_vpe_name as vpeUnit',
            'p.gm_show_qty_info as showStock',
            'p.products_vpe_status as showVpe',
            'p.products_startpage as showStartPage',
            'p.gm_show_weight as showWeight',
            'p.gm_sitemap_entry as showSiteMap',
            'p.gm_show_date_added as showRelease',
            'pa.products_id IS NOT NULL as isDownloadable',
            'p.products_quantity as quantity',
            'p.gm_graduated_qty as quantityInterval',
            'p.gm_min_order as orderMinQuantity',
            'qud.unit_name as quantityUnit',
            'gea.availability as availability',
            'pd.products_url as productUrl',
            'pd.products_meta_title as metaTitle',
            'pd.products_meta_description as metaDescription',
            'pd.products_meta_keywords as metaKeywords',
            'p.products_date_added as createdAt',
            'p.products_last_modified as modifiedAt',
            'p.products_date_available as availableAt',
            's.expires_date as expiresAt',
            'p.products_image as imagesMainUrl',
            'pd.gm_alt_text as imagesMainAltText',
            'm.manufacturers_name as manufacturerName',
            'mi.manufacturers_url as manufacturerUrl',
            'm.manufacturers_image as manufacturerImage',
            'p.products_tax_class_id as taxClassId',
            'p.gm_price_status as priceStatus',
            'p.use_properties_combis_shipping_time as useVariantsShippingTime',
            'count(ppc.products_properties_combis_id) > 0 as hasVariants',
        ];

        $productIds = $ids->toArray();

        $statement = $this->fromProducts($qb->select($selectArray), $ids, $settings->languageId())
            ->leftJoin(
                'p',
                sprintf(
                    '(%s)',
                    $this->connection->createQueryBuilder()
                                     ->select('a.products_id')
                                     ->from('products_attributes', 'a')
                                     ->innerJoin(
                                         'a',
                                         'products_attributes_download',
                                         'd',
                                         $qb->expr()->eq('a.products_attributes_id', 'd.products_attributes_id')
                                     )
                                     ->where($qb->expr()->in('a.products_id', $productIds))
                                     ->groupBy('a.products_id')
                                     ->getSQL()
                ),
                'pa',
                $qb->expr()->eq('p.products_id', 'pa.products_id')
            )
            ->leftJoin('p', 'products_properties_combis', 'ppc', 'p.products_id = ppc.products_id')
            ->groupBy('p.products_id');

        if ($sortOrder) {
            $statement->orderBy($sortOrder->value(), $sortOrder->sortDirection());
        } else {
            $statement->orderBy(
                'FIELD(p.products_id, ' . implode(',', $productIds) . ')'
            );
        }

        $statement->setFirstResult($pagination->mySqlOffset())
                  ->setMaxResults($pagination->itemsPerPage());

        return $statement->executeQuery()->fetchAllAssociative();
    }
    
    
    /**
     * Fetches the total count of datasets for the given product ids.
     *
     * @param ListingItemIds  $ids
     * @param ListingSettings $settings
     *
     * @return int
     */
    public function fetchTotal(ListingItemIds $ids, ListingSettings $settings): int
    {
        $qb        = $this->connection->createQueryBuilder();
        $statement = $this->fromProducts($qb->select('COUNT(*) as count'), $ids, $settings->languageId())->executeQuery();
        
        $value = $statement->fetchOne() ? : null;
        if (!$value) {
            $idsString = implode('", "', $ids->toArray());
            $message   = 'Could not count total datasets for product ids "' . $idsString . '"';
            
            throw new UnexpectedValueException($message);
        }
        
        return (int)$value;
    }
    
    
    /**
     * Adds some products table related statements to the query builder.
     *
     * The FROM statement is set to the products table, a join to the products_description table is added
     * and where expressions based on the given ids and language id is set up.
     *
     * Can be used for fetching the whole dataset, but it can also be used to fetch the totals of a dataset.
     *
     * @param QueryBuilder   $qb
     * @param ListingItemIds $ids
     * @param int            $languageId
     *
     * @return QueryBuilder
     */
    private function fromProducts(QueryBuilder $qb, ListingItemIds $ids, int $languageId): QueryBuilder
    {
        return $qb->from('products', 'p')
            ->leftJoin('p', 'products_description', 'pd', 'p.products_id = pd.products_id')
            ->leftJoin('p', 'products_item_codes', 'pic', 'p.products_id = pic.products_id')
            ->add('join', [
                'pic' => [
                    'joinType'      => 'left outer',
                    'joinTable'     => 'google_export_availability',
                    'joinAlias'     => 'gea',
                    'joinCondition' => "pic.google_export_availability_id = gea.google_export_availability_id AND gea.language_id = $languageId",
                ],
            ],    true)
            ->add('join', [
                'p' => [
                    'joinType'      => 'left outer',
                    'joinTable'     => 'products_quantity_unit',
                    'joinAlias'     => 'pqu',
                    'joinCondition' => 'p.products_id = pqu.products_id',
                ],
            ],    true)
            ->add('join', [
                'pqu' => [
                    'joinType'      => 'left outer',
                    'joinTable'     => 'quantity_unit_description',
                    'joinAlias'     => 'qud',
                    'joinCondition' => "pqu.quantity_unit_id = qud.quantity_unit_id AND qud.language_id = $languageId",
                ],
            ],    true)
            ->leftJoin('p', 'specials', 's', 'p.products_id = s.products_id')
            ->leftJoin('p', 'manufacturers', 'm', 'p.manufacturers_id = m.manufacturers_id')
            ->leftJoin('p', 'product_type_descriptions', 'ptd', 'p.product_type = ptd.product_type_id')
            ->leftJoin('p', 'products_xsell', 'xp', 'p.products_id = xp.products_id')
            ->add('join', [
                'p' => [
                    'joinType'      => 'left outer',
                    'joinTable'     => 'products_vpe',
                    'joinAlias'     => 'pv',
                    'joinCondition' => "p.products_vpe = pv.products_vpe_id AND pv.language_id = $languageId",
                ],
            ],    true)
            ->leftJoin('m',
                       'manufacturers_info',
                       'mi',
                       $qb->expr()->and($qb->expr()->eq('m.manufacturers_id', 'mi.manufacturers_id'),
                                         $qb->expr()->eq('mi.languages_id', $languageId)))
            ->where($qb->expr()->and($qb->expr()->eq('pd.language_id', $languageId),
                                      $qb->expr()->in('p.products_id', $ids->toArray()),
                                      $qb->expr()->eq('ptd.language_id', $languageId)));
    }
}
