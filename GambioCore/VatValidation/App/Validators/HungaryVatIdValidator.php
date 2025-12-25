<?php
/* --------------------------------------------------------------
   HungaryVatIdValidator.php 2022-04-21
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
 * Class HungaryVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class HungaryVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'hu';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        $number = str_replace('hu', '', strtolower($vatId));
        if (strlen($vatId) == 10) {
            if (strlen($number) == 8 && is_numeric($number)) {
                return true;
            }
            
            return false;
        }
        
        return false;
    }
}