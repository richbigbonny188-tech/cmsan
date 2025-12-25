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

namespace Gambio\Admin\Modules\Country\Services;

use Gambio\Admin\Modules\Country\Model\Collections\Countries;
use Gambio\Admin\Modules\Country\Model\Country;
use Gambio\Admin\Modules\Country\Services\Exceptions\CountryDoesNotExistException;

/**
 * Interface CountryReadService
 *
 * @package Gambio\Admin\Modules\Country\Services
 */
interface CountryReadService
{
    /**
     * Returns all available countries.
     *
     * @param int $languageId
     *
     * @return Countries
     */
    public function getCountries(int $languageId): Countries;
    
    
    /**
     * Returns all countries customers that are active.
     *
     * @param int $languageId
     *
     * @return Countries
     */
    public function getActiveCountries(int $languageId): Countries;
    
    
    /**
     * Returns a specific country based on the given ID.
     *
     * @param int $id
     * @param int $languageId
     *
     * @return Country
     *
     * @throws CountryDoesNotExistException
     */
    public function getCountryById(int $id, int $languageId): Country;
}