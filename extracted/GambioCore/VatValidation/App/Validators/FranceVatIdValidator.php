<?php
/* --------------------------------------------------------------
   FranceVatIdValidator.php 2022-04-21
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
 * Class FranceVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class FranceVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'fr';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        $number = substr(str_replace('fr', '', strtolower($vatId)), 2);
        if (strlen($vatId) == 13) {
            if (strlen($number) == 9 && is_numeric($number)) {
                return true;
            }
            
            return false;
        }
        
        return false;
    }
}