<?php
/*--------------------------------------------------------------
   CurrencyRepository.php 2022-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\App;

use Gambio\Admin\Modules\Currency\App\Data\CurrencyMapper;
use Gambio\Admin\Modules\Currency\App\Data\CurrencyReader;
use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Model\Currency;
use Gambio\Admin\Modules\Currency\Model\ValueObjects\CurrencyId;
use Gambio\Admin\Modules\Currency\Services\CurrencyRepository as CurrencyRepositoryInterface;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Class CurrencyRepository
 *
 * @package Gambio\Admin\Modules\Currency\App
 */
class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @var CurrencyReader
     */
    private $reader;
    
    /**
     * @var CurrencyMapper
     */
    private $mapper;
    
    
    /**
     * @param CurrencyReader $reader
     * @param CurrencyMapper $mapper
     */
    public function __construct(
        CurrencyReader $reader,
        CurrencyMapper $mapper
    ) {
        $this->reader = $reader;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCurrencies(Filters $filters, Sorting $sorting, Pagination $pagination): Currencies
    {
        $data = $this->reader->filterCurrencies($filters, $sorting, $pagination);
        
        return $this->mapper->mapCurrencies(...$data);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCurrenciesTotalCount(Filters $filters): int
    {
        return $this->reader->getCurrenciesTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCurrencyById(CurrencyId $currencyId): Currency
    {
        $data = $this->reader->getCurrencyById($currencyId);
        
        return $this->mapper->mapCurrency($data[0]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllCurrencies(): Currencies
    {
        $data = $this->reader->getAllCurrencies();
        
        return $this->mapper->mapCurrencies(...$data);
    }
}