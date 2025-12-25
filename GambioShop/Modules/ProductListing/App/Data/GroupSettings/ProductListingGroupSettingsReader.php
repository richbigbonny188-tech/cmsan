<?php
/* --------------------------------------------------------------
   ProductListingGroupSettingsReader.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\GroupSettings;

use Doctrine\DBAL\Connection;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\App\Data\Price\Components\ConvertStringToBooleanTrait;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CustomerGroupSettingsNotFoundException;

/**
 * Class ProductListingGroupSettingsReader
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingGroupSettingsReader
{
    private const TABLE_CUSTOMER       = 'customers';
    private const TABLE_CUSTOMER_GROUP = 'customers_status';
    
    private const CONFIG_KEY_DEFAULT_GUEST_ID              = 'configuration/DEFAULT_CUSTOMERS_STATUS_ID_GUEST';
    private const CONFIG_KEY_SHOW_NORMAL_PRICE_ON_DISCOUNT = 'gm_configuration/SHOW_OLD_DISCOUNT_PRICE';
    private const CONFIG_KEY_SHOW_OLD_SPECIAL_PRICE        = 'gm_configuration/SHOW_OLD_SPECIAL_PRICE';
    
    use ConvertStringToBooleanTrait;
    
    private Connection          $connection;
    private ConfigurationFinder $configurationFinder;
    
    
    /**
     * ProductListingGroupSettingsReader constructor.
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
     * Fetches the customer group id for the given customer id.
     * Throws an exception if no group id was found.
     *
     * @param int $customerId
     *
     * @return int
     * @throws CustomerGroupSettingsNotFoundException
     */
    public function fetchGroupId(int $customerId): int
    {
        $qb = $this->connection->createQueryBuilder();
        
        $where     = $qb->expr()->eq('customers_id', $qb->createNamedParameter($customerId));
        $statement = $qb->select('customers_status')->from(self::TABLE_CUSTOMER)->where($where)->executeQuery();
        
        $result = $statement->fetchAssociative();
        if (false === $result) {
            $message = "Can not find customer group id for customer with id '$customerId'";
            throw new CustomerGroupSettingsNotFoundException($message);
        }
        
        return (int)$result['customers_status'];
    }
    
    
    /**
     * Fetches customer group settings from the database.
     *
     * @param int $groupId
     * @param int $languageId
     *
     * @return array
     * @throws CustomerGroupSettingsNotFoundException
     */
    public function fetchGroupData(int $groupId, int $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        
        $groupIdEq    = $qb->expr()->eq('customers_status_id', $groupId);
        $languageIdEq = $qb->expr()->eq('language_id', $languageId);
        
        $where     = $qb->expr()->and($groupIdEq, $languageIdEq);
        $selects   = [
            'customers_status_discount',
            'customers_status_graduated_prices',
            'customers_status_show_price',
            'customers_status_discount_attributes',
        ];
        $statement = $qb->select($selects)->from(self::TABLE_CUSTOMER_GROUP)->where($where)->executeQuery();
        
        $data = $statement->fetchAssociative();
        if (false === $data) {
            $message = "Can not find customer group settings with group id '$groupId' and language id '$languageId'";
            throw new CustomerGroupSettingsNotFoundException($message);
        }
        $discount = is_array($data)
                    && array_key_exists('customers_status_discount',
                                        $data) ? (float)$data['customers_status_discount'] : 0.0;
        
        return [
            'groupDiscount'             => $discount,
            'isPersonalOfferEnabled'    => $this->isTrue($data['customers_status_graduated_prices'] ?? '0'),
            'isAllowedToSeePrices'      => $this->isTrue($data['customers_status_show_price'] ?? '0'),
            'isVariantsDiscountEnabled' => $this->isTrue($data['customers_status_discount_attributes'] ?? '0'),
        ];
    }
    
    
    /**
     * Fetches the guest customer group id from the shop configuration.
     *
     * @return int
     * @todo reevaluate if this still needs to be fetched or if it could be hardcoded
     */
    public function fetchGuestId(): int
    {
        return (int)$this->configurationFinder->get(self::CONFIG_KEY_DEFAULT_GUEST_ID);
    }
    
    
    /**
     * Fetches a configuration that determines whether the normal price should also be displayed for discounts.
     *
     * @return bool
     */
    public function showNormalPriceOnDiscount(): bool
    {
        return $this->isTrue($this->configurationFinder->get(self::CONFIG_KEY_SHOW_NORMAL_PRICE_ON_DISCOUNT));
    }
    
    
    /**
     * Fetches a configuration that determines whether the price without being a special should also be displayed.
     *
     * @return bool
     */
    public function showPriceBeforeSpecial(): bool
    {
        return $this->isTrue($this->configurationFinder->get(self::CONFIG_KEY_SHOW_OLD_SPECIAL_PRICE));
    }
}