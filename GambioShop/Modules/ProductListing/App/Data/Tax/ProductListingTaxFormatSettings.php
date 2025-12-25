<?php
/* --------------------------------------------------------------
   ProductListingTaxFormatSettings.php 2023-06-14
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
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Services\ConfigurationFinder;

/**
 * Class ProductListingTaxFormatSettings
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data
 */
class ProductListingTaxFormatSettings
{
    private const SETTINGS_ENABLED_REPRESENTATION = ['1', 'on', 'true'];
    
    private const CONFIGURATION_KEY_IS_TAX_FREE    = 'gm_configuration/TAX_INFO_TAX_FREE';
    private const CONFIGURATION_KEY_SHOW_TAX       = 'gm_configuration/DISPLAY_TAX';
    private const CONFIGURATION_KEY_SHOW_EMPTY_TAX = 'gm_configuration/DISPLAY_0_PROCENT_TAX';
    
    private const FIELD_SHOW_PRICE_TAX = 'customers_status_show_price_tax';
    private const FIELD_ADD_TAX        = 'customers_status_add_tax_ot';
    
    private Connection          $connection;
    private ConfigurationFinder $finder;
    
    
    /**
     * ProductListingTaxFormatSettings constructor.
     *
     * @param Connection          $connection
     * @param ConfigurationFinder $finder
     */
    public function __construct(Connection $connection, ConfigurationFinder $finder)
    {
        $this->connection = $connection;
        $this->finder     = $finder;
    }
    
    
    /**
     * Provides "is tax-free" setting from shop configuration.
     *
     * @return bool
     */
    public function isTaxFree(): bool
    {
        return $this->getConfigurationSetting(static::CONFIGURATION_KEY_IS_TAX_FREE);
    }
    
    
    /**
     * Provides "show tax" setting from shop configuration.
     *
     * @return bool
     */
    public function showTax(): bool
    {
        return $this->getConfigurationSetting(static::CONFIGURATION_KEY_SHOW_TAX);
    }
    
    
    /**
     * Provides "show tax if empty" setting from shop configuration.
     *
     * @return bool
     */
    public function showEmptyTax(): bool
    {
        return $this->getConfigurationSetting(static::CONFIGURATION_KEY_SHOW_EMPTY_TAX);
    }
    
    
    /**
     * Provides "show tax" setting for customer.
     *
     * @param int|null $customerId
     * @param int      $languageId
     *
     * @return bool
     */
    public function showPriceTax(?int $customerId, int $languageId): bool
    {
        return $this->getCustomerStatusSetting(static::FIELD_SHOW_PRICE_TAX, $customerId, $languageId);
    }
    
    
    /**
     * Provides "add tax" setting for customer.
     *
     * @param int|null $customerId
     * @param int      $languageId
     *
     * @return bool
     */
    public function addTax(?int $customerId, int $languageId): bool
    {
        return $this->getCustomerStatusSetting(static::FIELD_ADD_TAX, $customerId, $languageId);
    }
    
    
    /**
     * Returns a customer related setting.
     *
     * IMPORTANT: Never expose $customerStatusField in a public method
     * or to an argument of a public method, so it is not possible to
     * do SQL-Injections.
     *
     * @param string   $customerStatusField
     * @param int|null $customerId
     * @param int      $languageId
     *
     * @return bool
     */
    private function getCustomerStatusSetting(string $customerStatusField, ?int $customerId, int $languageId): bool
    {
        if ($customerId === null) {
            return $this->getGuestSetting($customerStatusField, $languageId);
        }
        
        $qb    = $this->connection->createQueryBuilder();
        $query = $qb->select("cs.$customerStatusField as value")
            ->from('customers_status', 'cs')
            ->leftJoin('cs', 'customers', 'c', 'cs.customers_status_id = c.customers_status')
            ->where($qb->expr()->and($qb->expr()->eq('c.customers_id', $customerId),
                                      $qb->expr()->eq('cs.language_id', $languageId)));
        
        try {
            $result = $query->executeQuery();
            $value  = $result->fetchAssociative()['value'] ?? null;
            
            return $this->fetchResult($value);
        } catch (Exception $e) {
            return false;
        }
    }
    
    
    /**
     * Returns a guest related setting.
     *
     * IMPORTANT: Never expose $customerStatusField in a public method
     * or to an argument of a public method, so it is not possible to
     * do SQL-Injections.
     *
     * @param string $customerStatusField
     * @param int    $languageId
     *
     * @return bool
     */
    private function getGuestSetting(string $customerStatusField, int $languageId): bool
    {
        $qb      = $this->connection->createQueryBuilder();
        $guestId = $this->finder->get('configuration/DEFAULT_CUSTOMERS_STATUS_ID_GUEST');
        
        $cStatusEqGuestID = $qb->expr()->eq('cs.customers_status_id', $guestId);
        $languageIdEq     = $qb->expr()->eq('cs.language_id', $languageId);
        
        $query = $qb->select("cs.$customerStatusField as value")->from('customers_status', 'cs')->where($qb->expr()
                                                                                                            ->and($cStatusEqGuestID,
                                                                                                                  $languageIdEq));
        
        try {
            $result = $query->executeQuery();
            $value  = $result->fetchAssociative()['value'] ?? null;
            
            return $this->fetchResult($value);
        } catch (Exception $e) {
            return false;
        }
    }
    
    
    /**
     * Fetches and converts the result into a boolean.
     *
     * @param string|null $value
     *
     * @return bool
     */
    private function fetchResult(?string $value): bool
    {
        if ($value === null) {
            return false;
        }
        
        return $this->isEnabled($value);
    }
    
    
    /**
     * Returns a setting from the shop configuration.
     *
     * @param string $configurationKey
     *
     * @return bool
     */
    private function getConfigurationSetting(string $configurationKey): bool
    {
        $result = $this->finder->get($configurationKey);
        if ($result === null) {
            return false;
        }
        
        return $this->isEnabled($result);
    }
    
    
    /**
     * Checks if the string $value represents a boolean TRUE value.
     *
     * @param string $value
     *
     * @return bool
     */
    private function isEnabled(string $value): bool
    {
        return in_array(strtolower($value), static::SETTINGS_ENABLED_REPRESENTATION);
    }
}