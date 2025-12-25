<?php
/* --------------------------------------------------------------
  cookie_purpose_is_active.php 2020-01-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceInterface;

/**
 * Checks whether the purpose is active in Gambio Admin > Modules > Cookie Consent > Purposes or not
 *
 * @param int $purposeId
 *
 * @return bool
 */
function cookie_purpose_is_active(int $purposeId): bool
{
    /** @var PurposeReaderServiceInterface $readService */
    static $readService;
    
    if ($readService === null) {
        
        $readService = StaticGXCoreLoader::getService('PurposeReader');
    }
    
    foreach ($readService->allPurposes() as $purpose) {
        
        $currentPurposeId = $purpose->id()->value();
        
        if ($currentPurposeId !== null && $currentPurposeId === $purposeId) {
            
            return $purpose->status()->isActive();
        }
    }
    
    return false;
}

