<?php
/*--------------------------------------------------------------------------------------------------
    CustomerCountryTranslationRepository.php 2022-09-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;


use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressCountry;

/**
 *
 */
interface CustomerAddressCountryTranslationRepository
{
    /**
     * @param string $countryIsoCode2
     *
     * @return CustomerAddressCountry
     */
    public function getCustomerCountryByIsoCode2(string $countryIsoCode2): CustomerAddressCountry;
}