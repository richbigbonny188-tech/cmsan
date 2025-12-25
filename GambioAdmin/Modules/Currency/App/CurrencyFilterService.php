<?php
/*--------------------------------------------------------------
   CurrencyFilterService.php 2022-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\App;

use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterFactory;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyRepository as CurrencyRepositoryInterface;
use Gambio\Admin\Modules\Currency\Services\Exceptions\InvalidCurrencyArgumentException;
use InvalidArgumentException;

/**
 * Class CurrencyFilterService
 *
 * @package Gambio\Admin\Modules\Currency\App
 */
class CurrencyFilterService implements CurrencyFilterServiceInterface
{
    /**
     * @var CurrencyFilterFactory
     */
    private $factory;
    
    /**
     * @var CurrencyRepositoryInterface
     */
    private $repository;
    
    
    /**
     * @param CurrencyFilterFactory       $factory
     * @param CurrencyRepositoryInterface $repository
     */
    public function __construct(CurrencyFilterFactory $factory, CurrencyRepositoryInterface $repository)
    {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCurrencies(
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): Currencies {
        
        try {
            return $this->repository->filterCurrencies($this->factory->createFilters($filters),
                                                       $this->factory->createSorting($sorting),
                                                       $this->factory->createPagination($limit, $offset));
        } catch (InvalidArgumentException $exception) {
            throw InvalidCurrencyArgumentException::createdFromPrevious($exception);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCurrenciesTotalCount(array $filters): int
    {
        try {
            return $this->repository->getCurrenciesTotalCount($this->factory->createFilters($filters));
        } catch (InvalidArgumentException $exception) {
            throw InvalidCurrencyArgumentException::createdFromPrevious($exception);
        }
    }
}