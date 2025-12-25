<?php
/* --------------------------------------------------------------
   BelgiumVatIdValidator.php 2022-04-21
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
 * Class BelgiumVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class BelgiumVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'be';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        if (strlen($vatId) != 12) {
            return false;
        }
        
        $number = str_replace('be', '', strtolower($vatId));
        
        if (strlen($number) == 10 && is_numeric($number)) {
            return true;
        }
        
        return false;
    }
}