<?php
/*--------------------------------------------------------------
   CountryReadService.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\App;

use Gambio\Admin\Modules\Country\Model\Collections\Countries;
use Gambio\Admin\Modules\Country\Model\Country;
use Gambio\Admin\Modules\Country\Services\CountryFactory;
use Gambio\Admin\Modules\Country\Services\CountryReadService as CountryReadServiceInterface;
use Gambio\Admin\Modules\Country\Services\CountryRepository as CountryRepositoryInterface;

/**
 * Class CountryReadService
 *
 * @package Gambio\Admin\Modules\Country\App
 */
class CountryReadService implements CountryReadServiceInterface
{
    private CountryRepositoryInterface $repository;
    private CountryFactory             $factory;
    
    
    /**
     * @param CountryRepositoryInterface $repository
     * @param CountryFactory             $factory
     */
    public function __construct(
        CountryRepositoryInterface $repository,
        CountryFactory $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCountries(int $languageId): Countries
    {
        return $this->repository->getCountries($languageId);
    }

    /**
     * @inheritDoc
     */
    public function getActiveCountries(int $languageId): Countries
    {
        return $this->repository->getActiveCountries($languageId);
    }

    /**
     * @inheritDoc
     */
    public function getCountryById(int $id, int $languageId): Country
    {
        return $this->repository->getCountryById($this->factory->createCountryId($id), $languageId);
    }
}