<?php
/*--------------------------------------------------------------
   CountryRepository.php 2022-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\App;

use Gambio\Admin\Modules\Country\App\Data\CountryMapper;
use Gambio\Admin\Modules\Country\App\Data\CountryReader;
use Gambio\Admin\Modules\Country\Model\Collections\Countries;
use Gambio\Admin\Modules\Country\Model\Country;
use Gambio\Admin\Modules\Country\Model\ValueObjects\CountryId;
use Gambio\Admin\Modules\Country\Services\CountryRepository as CountryRepositoryInterface;
use Gambio\Admin\Modules\Country\Services\Exceptions\CountryDoesNotExistException;

/**
 * Class CountryRepository
 *
 * @package Gambio\Admin\Modules\Country\App
 */
class CountryRepository implements CountryRepositoryInterface
{
    private CountryReader $reader;
    private CountryMapper $mapper;
    
    
    /**
     * @param CountryReader $reader
     * @param CountryMapper $mapper
     */
    public function __construct(
        CountryReader $reader,
        CountryMapper $mapper
    ) {
        $this->reader = $reader;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCountries(int $languageId): Countries
    {
        return $this->mapper->mapCountries($this->reader->getCountries($languageId));
    }

    /**
     * @inheritDoc
     */
    public function getActiveCountries(int $languageId): Countries
    {
        return $this->mapper->mapCountries($this->reader->getActiveCountries($languageId));
    }

    /**
     * @inheritDoc
     */
    public function getCountryById(CountryId $id, int $languageId): Country
    {
        return $this->mapper->mapCountry($this->reader->getCountryById($id, $languageId));
    }
}