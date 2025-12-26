<?php
/* --------------------------------------------------------------
   AustriaVatIdValidator.php 2022-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\VatValidation\App\Validators;

/**
 * Class AustriaVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class AustriaVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'at';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        if (strlen($vatId) != 11 || strtoupper($vatId[2]) != 'U') {
            return false;
        }
        
        $number = substr(str_replace('at', '', strtolower($vatId)), 1);
        
        if (strlen($number) == 8 && is_numeric($number)) {
            return true;
        }
        
        return false;
    }
}