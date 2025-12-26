<?php
/* --------------------------------------------------------------
   ManualVatIdValidator.php 2022-04-21
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
 * Interface ManualVatIdValidator
 *
 * @package Gambio\Core\VatValidation\App\Validators
 */
interface ManualVatIdValidator
{
    /**
     * @return string
     */
    public function validatedCountryIsoCode(): string;
    
    
    /**
     * @param string $vatId
     *
     * @return bool
     */
    public function validateVatId(string $vatId): bool;
}