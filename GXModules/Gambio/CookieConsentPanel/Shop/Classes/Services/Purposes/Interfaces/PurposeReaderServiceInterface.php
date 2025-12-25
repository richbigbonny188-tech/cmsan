<?php
/* --------------------------------------------------------------
  PurposeReaderServiceInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeReaderServiceInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeReaderServiceInterface
{
    /**
     * @param int $languageId
     *
     * @return CategoryInterface[]
     */
    public function categories(int $languageId): array;
    
    /**
     * @param int $languageId
     *
     * @return PurposeInterface[]
     */
    public function activePurposes(int $languageId): array;
    
    
    /**
     * @return PurposeInterface[]
     */
    public function allPurposes(): array;
}