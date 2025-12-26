<?php
/* --------------------------------------------------------------
   IrelandVatIdValidator.php 2022-04-21
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
 * Class IrelandVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class IrelandVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'ie';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        $number = str_replace('ie', '', strtolower($vatId));
        if (strlen($vatId) == 10) {
            if (strlen($number) == 8) {
                return true;
            }
            
            return false;
        }
        
        return false;
    }
}