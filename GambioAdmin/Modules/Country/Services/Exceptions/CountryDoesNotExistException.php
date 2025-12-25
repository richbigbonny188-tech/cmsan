<?php
/*--------------------------------------------------------------
   CountryDoesNotExistException.php 2022-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Country\Model\ValueObjects\CountryId;

/**
 * Class CountryDoesNotExistException
 *
 * @package Gambio\Admin\Modules\Country\Services\Exceptions
 */
class CountryDoesNotExistException extends Exception
{
    /**
     * @param CountryId $countryId
     *
     * @return CountryDoesNotExistException
     */
    public static function forCountryId(CountryId $countryId): CountryDoesNotExistException
    {
        $message = 'No country was found with the id "%s"';
        
        return new self(sprintf($message, $countryId->value()));
    }
}