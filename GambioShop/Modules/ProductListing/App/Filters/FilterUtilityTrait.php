<?php
/* --------------------------------------------------------------
   FilterUtilityTrait.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Filters;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;
use UnexpectedValueException;

/**
 * Trait FilterUtilityTrait
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Filters
 */
trait FilterUtilityTrait
{
    
    /**
     * @param QueryBuilder        $qb
     * @param ListingSettings     $settings
     * @param ConfigurationFinder $configurationFinder
     * @param Connection          $connection
     *
     * @return QueryBuilder
     */
    private function prepareDefaultStatement(
        QueryBuilder        $qb,
        ListingSettings     $settings,
        ConfigurationFinder $configurationFinder,
        Connection          $connection
    ): QueryBuilder {
        $this->andWhereIsEnabled($qb);
        
        if ($this->isFskHidden($settings, $configurationFinder, $connection)) {
            $qb = $this->andWhereHiddenFsk18($qb);
        }
        
        if ($this->isGroupCheckEnabled($configurationFinder)) {
            $customerGroupId = $this->customerIdToStatusId($settings->customerId(),
                                                           $connection,
                                                           $configurationFinder);
            $qb              = $this->andWhereCustomerGroupCheck($qb, $customerGroupId);
        }
        
        return $qb;
    }
    
    
    /**
     * Checks if fsk18 should be hidden for customers of given settings.
     *
     * @param ListingSettings     $settings
     * @param ConfigurationFinder $configurationFinder
     * @param Connection          $connection
     *
     * @return bool
     */
    private function isFskHidden(
        ListingSettings     $settings,
        ConfigurationFinder $configurationFinder,
        Connection          $connection
    ): bool {
        $customerId = $settings->customerId();
        $customerId = $customerId ? : $this->getDefaultGuestId($configurationFinder);
        
        $languageId = $settings->languageId();
        
        $qb     = $connection->createQueryBuilder();
        $result = $qb->select('cs.customers_fsk18_display')
            ->from('customers', 'c')
            ->leftJoin('c', 'customers_status', 'cs', 'c.customers_status = cs.customers_status_id')
            ->where($qb->expr()->and($qb->expr()->eq('c.customers_id', $customerId),
                                      $qb->expr()->eq('cs.language_id', $languageId)))
            ->executeQuery();
        $data   = $result->fetchAssociative();
        $value  = $data['customers_fsk18_display'] ?? '0';
        
        return $value === '0';
    }
    
    
    /**
     * Adds an and where statement to hide fsk18 products.
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function andWhereHiddenFsk18(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere($qb->expr()->eq('products_fsk18', '0'));
    }
    
    
    /**
     * Adds an and where statement to show only startpage products.
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function andWhereIsStartpage(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere($qb->expr()->eq('products_startpage', '1'));
    }
    
    
    /**
     * Adds an and where statement to show only enabled products.
     *
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function andWhereIsEnabled(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere($qb->expr()->eq('products_status', '1'));
    }
    
    
    /**
     * Transforms an optional customer id into the related customer status id.
     *
     * @param int|null            $customerId
     * @param Connection          $connection
     * @param ConfigurationFinder $configurationFinder
     *
     * @return int
     */
    private function customerIdToStatusId(
        ?int                $customerId,
        Connection          $connection,
        ConfigurationFinder $configurationFinder
    ): int {
        if (!$customerId) {
            return $this->getDefaultGuestId($configurationFinder);
        }
        
        $qb     = $connection->createQueryBuilder();
        $result = $qb->select('customers_status')
            ->from('customers')
            ->where($qb->expr()
                        ->eq('customers_id', $customerId))
            ->executeQuery();
        $data   = $result->fetchAssociative();
        $value  = $data['customers_status'] ?? null;
        
        if (!$value && !is_numeric($value)) { # customer status with id 0 is a valid case
            throw new UnexpectedValueException("Could not find customer status for customer with id ($customerId)");
        }
        
        return (int)$value;
    }
    
    
    /**
     * Returns the default guest id.
     *
     * @param ConfigurationFinder $configurationFinder
     *
     * @return int
     */
    private function getDefaultGuestId(ConfigurationFinder $configurationFinder): int
    {
        $config = $configurationFinder->get('configuration/DEFAULT_CUSTOMERS_STATUS_ID_GUEST');
        if (!$config) {
            throw new UnexpectedValueException("Missing default customer status configuration value");
        }
        
        return (int)$config;
    }
    
    
    /**
     * Checks if customer group check configuration is enabled.
     *
     * @param ConfigurationFinder $configurationFinder
     *
     * @return bool
     */
    private function isGroupCheckEnabled(ConfigurationFinder $configurationFinder): bool
    {
        $value = $configurationFinder->get('configuration/GROUP_CHECK');
        if (!$value) {
            return false;
        }
        $valid = ['true', '1', 'on', 'enabled'];
        
        return in_array(strtolower($value), $valid, true);
    }
    
    
    /**
     * Adds an and where statement for the product group check.
     *
     * @param QueryBuilder $qb
     * @param int          $customerGroupId
     *
     * @return QueryBuilder
     */
    private function andWhereCustomerGroupCheck(QueryBuilder $qb, int $customerGroupId): QueryBuilder
    {
        $key = "group_permission_$customerGroupId";
        
        return $qb->andWhere($qb->expr()->eq($key, '1'));
    }
}