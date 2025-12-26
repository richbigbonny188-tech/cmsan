<?php
/* --------------------------------------------------------------
  PurposeUpdateDtoInterface.php 2020-05-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeUpdateDtoInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeUpdateDtoInterface
{
    /**
     * @return int
     */
    public function categoryId(): int;

    
    /**
     * @return array
     */
    public function descriptions(): array;
    
    
    /**
     * @param int $languageId
     *
     * @return string|null
     */
    public function description(int $languageId);
    
    
    /**
     * @return array
     */
    public function names(): array;
    
    
    /**
     * @param int $languageId
     *
     * @return string|null
     */
    public function name(int $languageId);
    
    
    /**
     * @return bool
     */
    public function status(): bool;
    

    /**
     * @return string
     */
    public function alias(): ?string;
    
    
    /**
     * @return int
     */
    public function id(): ?int;
}