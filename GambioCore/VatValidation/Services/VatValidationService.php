<?php
/* --------------------------------------------------------------
   VatValidationService.php 2024-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\VatValidation\Services;

/**
 * Interface VatValidationService
 *
 * @package Gambio\Core\VatValidation\Services
 */
interface VatValidationService
{
    /**
     * @param string $vatId
     *
     * @return bool
     */
    public function validateVatId(string $vatId): bool;
    
    
    /**
     * @param string $vatId
     *
     * @return bool
     */
    public function isVatIdEmpty(string $vatId): bool;
}