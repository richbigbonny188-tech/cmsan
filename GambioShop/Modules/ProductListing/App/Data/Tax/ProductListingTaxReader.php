<?php
/* --------------------------------------------------------------
   ProductListingTaxReader.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Tax;

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class ProductListingTaxReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxReader
{
    private const CONFIGURATION_KEY_STORE_COUNTRY = 'configuration/STORE_COUNTRY';
    
    private Connection          $connection;
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * ProductListingTaxReader constructor.
     *
     * @param Connection          $connection
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(Connection $connection, ConfigurationFinder $configurationFinder)
    {
        $this->connection          = $connection;
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * Fetches tax class information from the database based on the given tax class id.
     *
     * @param int $taxClassId
     *
     * @return array|null
     */
    public function fetchClass(int $taxClassId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        
        $select = [
            'tc.tax_class_title as title',
            'tc.tax_class_description as description',
        ];
        
        $result = $qb->select($select)
            ->from('tax_class', 'tc')
            ->where($qb->expr()->eq('tc.tax_class_id', $taxClassId))
            ->executeQuery();
        
        if (is_array($data = $result->fetchAssociative())) {
            return $data;
        }
        
        return null;
    }
    
    
    /**
     * Fetch tax rates based on the given tax class id and optional customer id.
     *
     * The customer id is used to get the country id for tax rates.
     * Uses the configuration "configuration/STORE_COUNTRY" when no customer id was provided.
     *
     * @param int      $taxClassId
     * @param int|null $customerId
     *
     * @return array
     */
    public function fetchRates(int $taxClassId, ?int $customerId): array
    {
        $countryId = $this->getCountryId($customerId);
        $qb        = $this->connection->createQueryBuilder();
        
        $select = [
            'tr.tax_description as description',
            'tr.tax_rate as rate',
            'tr.tax_priority as priority',
        ];
        $result = $qb->select($select)
            ->from('tax_rates', 'tr')
            ->leftJoin('tr', 'zones_to_geo_zones', 'ztgz', 'tr.tax_zone_id = ztgz.geo_zone_id')
            ->where($qb->expr()->and($qb->expr()->eq('ztgz.zone_country_id', $countryId),
                                      $qb->expr()->eq('tr.tax_class_id', $taxClassId)))
            ->executeQuery();
        
        return $result->fetchAllAssociative();
    }
    
    
    /**
     * Returns the country id.
     *
     * Uses the configuration "configuration/STORE_COUNTRY" when no customer id was provided or
     * the customers default address does not contain a valid country id.
     *
     * @param int|null $customerId
     *
     * @return int
     */
    private function getCountryId(?int $customerId): int
    {
        if (!$customerId) {
            return (int)$this->configurationFinder->get(self::CONFIGURATION_KEY_STORE_COUNTRY);
        }
        
        $qb     = $this->connection->createQueryBuilder();
        $result = $qb->select('entry_country_id')
            ->from('address_book', 'ab')
            ->join('ab', 'customers', 'c', 'ab.customers_id = c.customers_default_address_id')
            ->where($qb->expr()->eq('c.customers_id', $qb->createNamedParameter($customerId)))
            ->executeQuery();
        
        $countryId = $result->fetchAssociative()['entry_country_id'] ?? null;
        if (!$countryId) {
            return (int)$this->configurationFinder->get(self::CONFIGURATION_KEY_STORE_COUNTRY);
        }
        
        return (int)$countryId;
    }
}