<?php
/* --------------------------------------------------------------
   ProductListingCurrencyRepository.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Shop\Modules\ProductListing\App\Data\Currency;

use Doctrine\DBAL\Connection;
use Gambio\Shop\Modules\ProductListing\App\Exceptions\CurrencyNotFoundException;
use Gambio\Shop\Modules\ProductListing\Model\ValueObjects\InternalToBeRenamed\ListingItemCurrency;

/**
 * Class ProductListingCurrencyRepository
 *
 * @package Gambio\Shop\Modules\ProductListing\App\Data\Price
 */
class ProductListingCurrencyRepository
{
    private Connection $connection;
    private array      $currencyCache = [];
    
    
    /**
     * ProductListingCurrencyRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Provides currency information for a listing.
     * In memory caches calls with same arguments.
     *
     * @param string $currencyCode
     *
     * @return ListingItemCurrency
     * @throws CurrencyNotFoundException
     */
    public function getCurrency(string $currencyCode): ListingItemCurrency
    {
        if (!array_key_exists($currencyCode, $this->currencyCache)) {
            $this->currencyCache[$currencyCode] = $this->fetchCurrency($currencyCode);
        }
        
        return $this->currencyCache[$currencyCode];
    }
    
    
    /**
     * Fetches a currency by currency code from the database.
     *
     * @param string $currencyCode
     *
     * @return ListingItemCurrency
     * @throws CurrencyNotFoundException
     */
    private function fetchCurrency(string $currencyCode): ListingItemCurrency
    {
        $qb = $this->connection->createQueryBuilder();
        
        $where = $qb->expr()->eq('code', $qb->createNamedParameter($currencyCode));
        
        $statement = $qb->select('code, decimal_point as dp, thousands_point as tp, decimal_places as dc, value, symbol_left as sl, symbol_right as sr')
            ->from('currencies')
            ->where($where)
            ->orderBy('currencies_id')
            ->setMaxResults(1)
            ->executeQuery();
        
        $result = $statement->fetchAssociative();
        if (false === $result) {
            $message = "Can not find currency with currency code '$currencyCode'";
            throw new CurrencyNotFoundException($message);
        }
        
        $value         = (float)$result['value'];
        $decimalPlaces = (int)$result['dc'];
        
        return new ListingItemCurrency($result['code'],
                                       $value,
                                       $result['dp'],
                                       $result['tp'],
                                       $decimalPlaces,
                                       $result['sl'],
                                       $result['sr']);
    }
}