<?php
/* --------------------------------------------------------------
   GreatBritainVatIdValidator.php 2022-04-21
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
 * Class GreatBritainVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class GreatBritainVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'gb';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        $number = str_replace('gb', '', strtolower($vatId));
        if (strlen($vatId) == 11) {
            if (strlen($number) == 9) {
                return true;
            }
            
            return false;
        } elseif (strlen($vatId) == 14) {
            if (strlen($number) == 12) {
                return true;
            }
            
            return false;
        }
        
        return false;
    }
}