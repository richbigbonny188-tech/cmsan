<?php
/*
 * --------------------------------------------------------------
 *   ProductPriceConversionReader.php 2023-06-09
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2023 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Price\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class ProductPriceConversionReader
 *
 * @package Gambio\Admin\Modules\Price\App\Data
 */
class ProductPriceConversionReader
{
    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * @param ConfigurationFinder $configurationFinder
     * @param Connection          $connection
     */
    public function __construct(ConfigurationFinder $configurationFinder, Connection $connection)
    {
        $this->configurationFinder = $configurationFinder;
        $this->connection          = $connection;
    }
    
    
    /**
     * checks if admin gross prices configuration is enabled
     *
     * @return bool
     */
    public function isAdminGrossEnabled(): bool
    {
        return filter_var($this->configurationFinder->get('configuration/PRICE_IS_BRUTTO', 'true'),
                          FILTER_VALIDATE_BOOLEAN);
    }
    
    
    /**
     * @param int $productId
     *
     * @return array
     * @throws Exception
     */
    public function getProductTaxes(int $productId): array
    {
        $taxId = $this->getProductTaxClassId($productId);
        if ($taxId !== null) {
            $countryId  = $this->getStoreCountryId();
            $taxZoneIds = $this->getCountryTaxZoneIds($countryId);
            $taxes      = $this->getTaxRatesByZones($taxId, $taxZoneIds);
        }
        
        return $taxes ?? [];
    }
    
    
    /**
     * @param int $productId
     *
     * @return int|null
     * @throws Exception
     */
    private function getProductTaxClassId(int $productId): ?int
    {
        $taxClassId = $this->connection->createQueryBuilder()
            ->select('products_tax_class_id')
            ->from('products')
            ->where('products_id = ?')
            ->setParameter(0, $productId)
            ->executeQuery()
            ->fetchOne();
        
        return ($taxClassId) ? (int)$taxClassId : null;
    }
    
    
    /**
     * @return int
     */
    private function getStoreCountryId(): int
    {
        return (int)$this->configurationFinder->get('configuration/STORE_COUNTRY');
    }
    
    
    /**
     * @param int $countryId
     *
     * @return array
     * @throws Exception
     */
    private function getCountryTaxZoneIds(int $countryId): array
    {
        $zoneIds = [];
        $rows    = $this->connection->createQueryBuilder()
            ->select('geo_zone_id')
            ->from('zones_to_geo_zones')
            ->where('zone_country_id = ?')
            ->setParameter(0, $countryId)
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($rows as $row) {
            $zoneIds[] = (int)$row['geo_zone_id'];
        }
        
        return $zoneIds;
    }
    
    
    /**
     * @param int   $taxId
     * @param array $taxZoneIds
     *
     * @return array
     * @throws Exception
     */
    private function getTaxRatesByZones(int $taxId, array $taxZoneIds = []): array
    {
        $rates = [];
        $rows  = $this->connection->createQueryBuilder()
            ->select('tax_priority', 'tax_rate')
            ->from('tax_rates')
            ->where('tax_class_id = ?')
            ->andWhere('tax_zone_id IN (?)')
            ->setParameter(0, $taxId)
            ->setParameter(1, $taxZoneIds, Connection::PARAM_STR_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($rows as $row) {
            $priority = (int)$row['tax_priority'];
            $rate     = (float)$row['tax_rate'];
            // Tax rates at the same priority are added, other rates are compounded.
            isset($rates[$priority]) ? $rates[$priority] += $rate : $rates[$priority] = $rate;
        }
        
        return $rates;
    }
}