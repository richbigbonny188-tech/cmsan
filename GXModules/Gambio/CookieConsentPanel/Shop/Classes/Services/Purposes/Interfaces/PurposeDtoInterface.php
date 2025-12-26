<?php
/* --------------------------------------------------------------
  PurposeDataTransferObjectInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeDataTransferObjectInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeDtoInterface
{
    /**
     * @return int
     */
    public function categoryId(): int;
    
    
    /**
     * @param int $languageId
     *
     * @return string
     */
    public function description(int $languageId): ?string;
    
    
    /**
     * @return array
     */
    public function descriptions(): array;
    
    
    /**
     * @return array
     */
    public function categoryNames(): array;
    
    
    /**
     * @param int $languageId
     *
     * @return string
     */
    public function categoryName(int $languageId): ?string;
    
    
    /**
     * @return array
     */
    public function names(): array;
    
    
    /**
     * @param int $languageId
     *
     * @return string
     */
    public function name(int $languageId): ?string;
    
    
    /**
     * @return bool
     */
    public function status(): bool;
    
    
    /**
     * @return bool
     */
    public function deletable(): bool;
    
    
    /**
     * @return string
     */
    public function alias(): ?string;
    
    
    /**
     * @return int
     */
    public function id(): ?int;
}