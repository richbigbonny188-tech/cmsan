<?php
/*--------------------------------------------------------------
   CurrencyReadService.php 2022-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Currency\App;

use Gambio\Admin\Modules\Currency\Model\Collections\Currencies;
use Gambio\Admin\Modules\Currency\Model\Currency;
use Gambio\Admin\Modules\Currency\Services\CurrencyFactory;
use Gambio\Admin\Modules\Currency\Services\CurrencyReadService as CurrencyReadServiceInterface;
use Gambio\Admin\Modules\Currency\Services\CurrencyRepository as CurrencyRepositoryInterface;
use Gambio\Admin\Modules\Currency\Services\Exceptions\InvalidCurrencyArgumentException;
use InvalidArgumentException;

/**
 * Class CurrencyReadService
 *
 * @package Gambio\Admin\Modules\Currency\App
 */
class CurrencyReadService implements CurrencyReadServiceInterface
{
    /**
     * @var CurrencyRepositoryInterface
     */
    private $repository;
    
    /**
     * @var CurrencyFactory
     */
    private $factory;
    
    
    /**
     * @param CurrencyRepositoryInterface $repository
     * @param CurrencyFactory             $factory
     */
    public function __construct(
        CurrencyRepositoryInterface $repository,
        CurrencyFactory             $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCurrencyById(int $currencyId): Currency
    {
        try {
            $currencyId = $this->factory->createCurrencyId($currencyId);
            
            return $this->repository->getCurrencyById($currencyId);
        } catch (InvalidArgumentException $exception) {
            throw InvalidCurrencyArgumentException::createdFromPrevious($exception);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllCurrencies(): Currencies
    {
        try {
            return $this->repository->getAllCurrencies();
        } catch (InvalidArgumentException $exception) {
            throw InvalidCurrencyArgumentException::createdFromPrevious($exception);
        }
    }
}