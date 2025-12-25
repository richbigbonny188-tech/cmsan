<?php
/* --------------------------------------------------------------
   CyprusVatIdValidator.php 2022-04-21
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
 * Class CyprusVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
class CyprusVatIdValidator implements ManualVatIdValidator
{
    /**
     * @inheritDoc
     */
    public function validatedCountryIsoCode(): string
    {
        return 'cy';
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateVatId(string $vatId): bool
    {
        if (strlen($vatId) != 11) {
            return false;
        }
        
        $number = str_replace('cy', '', strtolower($vatId));
        
        if (strlen($number) == 9) {
            return true;
        }
        
        return false;
    }
}