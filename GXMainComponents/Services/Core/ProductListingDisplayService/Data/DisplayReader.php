<?php
/* --------------------------------------------------------------
  DisplayReader.php 2023-12-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\ListingSettings;

/**
 * Class DisplayReader
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class DisplayReader
{
    private const CUSTOMER_STATUS_ID_GUEST = 1;
    
    
    /**
     * @param ConfigurationFinder $configurationFinder
     * @param Connection          $connection
     */
    public function __construct(private ConfigurationFinder $configurationFinder, private Connection $connection)
    {
    }
    
    
    /**
     * Fetches locale information for specific language.
     *
     * @param int $languageId
     *
     * @return array|null
     * @throws Exception
     */
    public function fetchLocale(int $languageId): ?array
    {
        $qb      = $this->connection->createQueryBuilder();
        $results = $qb->select(
            'l.languages_id AS language_id',
            'l.code AS language_code',
            'l.directory AS language_directory',
            'c.code AS currency_code',
            'c.decimal_places AS decimals',
            'c.decimal_point AS decimal_separator',
            'c.thousands_point AS thousands_separator',
        )
            ->from('languages', 'l')
            ->where($qb->expr()->eq('l.languages_id',$languageId))
            ->leftJoin('l', 'currencies', 'c', 'l.language_currency = c.code')
            ->executeQuery()
            ->fetchAssociative();
        
        return $results ? : null;
    }
    
    
    /**
     * Fetches products SEO Boost active status from configurations.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoProductBoostIsEnabled(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/GM_SEO_BOOST_PRODUCTS')
        );
    }
    
    
    /**
     * Evaluates <String> or <NULL> value for boolean reformat.
     * Parse strings like 'true', '1' to TRUE. Returns FALSE for any other value.
     *
     * @param string|null $value
     *
     * @return bool
     */
    private function prepareBoolValue(?string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
    
    
    /**
     * Fetches content SEO Boost active status from configurations.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoContentBoostIsEnabled(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/GM_SEO_BOOST_CONTENT')
        );
    }
    
    
    /**
     * Fetches short urls SEO Boost active status from configurations.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoShortUrlsBoostIsEnabled(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/GM_SEO_BOOST_SHORT_URLS')
        );
    }
    
    
    /**
     * Fetches SEO configurations for using friendly URLs or default shop URLs.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoUseFriendlyUrls(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/GM_SEO_BOOST_SHORT_URLS')
        );
    }
    
    
    /**
     * Fetches SEO configurations for using booster language or shop language.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoUseBoostLanguage(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/USE_SEO_BOOST_LANGUAGE_CODE')
        );
    }
    
    
    /**
     * Fetches SEO configurations for removing index.php from URL.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchSeoSuppressIndexUsage(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/SUPPRESS_INDEX_IN_URL')
        );
    }
    
    
    /**
     * Fetches Shipping Info content group ID from configurations.
     *
     * @return int Default return value '0'
     */
    public function fetchShippingInfoContentGroupId(): int
    {
        return $this->prepareIntValue(
            $this->configurationFinder->get('configuration/SHIPPING_INFOS')
        );
    }
    
    
    /**
     * Evaluates <String> or <NULL> value for integer reformat.
     * Parse numeric strings like <Int>, <Float> to integers. Returns 0 for any other value.
     *
     * @param string|null $value
     *
     * @return int
     */
    private function prepareIntValue(?string $value): int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        
        return 0;
    }
    
    
    /**
     * Fetches localized content from content_manager by content group ID.
     *
     * @param int $contentGroupId
     * @param int $languageId
     *
     * @return array|null
     * @throws Exception
     */
    public function fetchContent(int $contentGroupId, int $languageId): ?array
    {
        $qb      = $this->connection->createQueryBuilder();
        $results = $qb->select(
            'cm.content_id AS content_id',
            'cm.content_group AS content_group',
            'cm.content_title AS content_title',
        )
                      ->from('content_manager', 'cm')
                      ->where(
                          $qb->expr()->and(
                              $qb->expr()->eq('cm.content_group', $contentGroupId),
                              $qb->expr()->eq('cm.languages_id', $languageId)
                          )
                      )
                      ->executeQuery()
                      ->fetchAssociative();

        return $results ? : null;
    }
    
    
    /**
     * Fetches products thumbnail width from configurations.
     *
     * @return int Default return value '0'
     */
    public function fetchProductThumbnailWidth(): int
    {
        return $this->prepareIntValue(
            $this->configurationFinder->get('configuration/PRODUCT_IMAGE_THUMBNAIL_WIDTH')
        );
    }
    
    
    /**
     * Fetches products thumbnail height from configurations.
     *
     * @return int Default return value '0'
     */
    public function fetchProductThumbnailHeight(): int
    {
        return $this->prepareIntValue(
            $this->configurationFinder->get('configuration/PRODUCT_IMAGE_THUMBNAIL_HEIGHT')
        );
    }
    
    
    /**
     * Fetches a check if shipping could be displayed from configurations.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchShippingIsShown(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('configuration/SHOW_SHIPPING')
        );
    }
    
    
    /**
     * Fetches a check if shipping is active from configurations.
     *
     * @return bool Default return value 'FALSE'
     */
    public function fetchShippingIsActive(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('configuration/ACTIVATE_SHIPPING_STATUS')
        );
    }
    
    
    /**
     * Fetches a check if prices could be displayed for specific customer & language.
     *
     * @param ListingSettings $settings
     *
     * @return bool
     * @throws Exception
     */
    public function fetchCanShowPrices(ListingSettings $settings): bool
    {
        $qb = $this->connection->createQueryBuilder();

        $query   = $qb->select('cs.customers_status_show_price AS canShowPrices')
                      ->from('customers_status', 'cs');
        $where[] = $qb->expr()->eq('cs.language_id', $settings->languageId());
        
        if ($customerId = $settings->customerId()) {
            $query->leftJoin('cs', 'customers', 'c', 'cs.customers_status_id = c.customers_status');
            $where[] = $qb->expr()->eq('c.customers_id', $customerId);
        } else {
            $where[] = $qb->expr()->eq('cs.customers_status_id', self::CUSTOMER_STATUS_ID_GUEST);
        }
        
        $query->where($qb->expr()->and(...$where));
        
        $results = $query->executeQuery()->fetchAssociative();
        
        return $this->prepareBoolValue($results ? $results['canShowPrices'] : null);
    }
    
    
    /**
     * Fetches a check if tax information could be displayed from configurations.
     *
     * @return bool
     */
    public function fetchCanShowTaxInfo(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/DISPLAY_TAX')
        );
    }
    
    
    /**
     * Fetches a check if TAX_FREE is enabled from configurations.
     *
     * @return bool
     */
    public function fetchTaxIsFree(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/TAX_INFO_TAX_FREE')
        );
    }
    
    
    /**
     * Fetches a check if SHOW_NO_TAXES_VALUE could be displayed from configurations.
     *
     * @return bool
     */
    public function fetchCanShowNoTaxValue(): bool
    {
        return $this->prepareBoolValue(
            $this->configurationFinder->get('gm_configuration/DISPLAY_0_PROCENT_TAX')
        );
    }
    
    
    /**
     * @param string $filename
     *
     * @return array
     */
    public function fetchImageDimensions(string $filename): array
    {
        $columns = ['products_image_w', 'products_image_h'];
        $result  = $this->connection->createQueryBuilder()
            ->select(implode(',', $columns))
            ->from('products')
            ->where('products_image = :image')
            ->setParameter('image', $filename)
            ->setMaxResults(1)
            ->executeQuery();
        
        if ($result->rowCount() === 0) {
            return [0, 0];
        }
        
        ['products_image_w' => $width, 'products_image_h' => $height] = $result->fetch();
        
        return [(int)$width, (int)$height];
    }
    
    
    /**
     * Fetches the currency settings by the given code
     *
     * @param string $currencyCode
     *
     * @return array|null
     */
    public function fetchCurrencySettingsByCode(string $currencyCode): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('code AS currency_code',
                     'decimal_places AS decimals',
                     'decimal_point AS decimal_separator',
                     'thousands_point AS thousands_separator')
            ->from('currencies')
            ->where('code = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->executeQuery()
            ->fetchAssociative();
        
        return $result ? : null;
    }
}