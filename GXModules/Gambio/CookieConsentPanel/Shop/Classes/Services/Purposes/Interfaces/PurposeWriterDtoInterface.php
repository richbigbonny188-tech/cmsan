<?php
/* --------------------------------------------------------------
  PurposeWriterDataTransferObjectInterface.php 2020-01-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeWriterDataTransferObjectInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeWriterDtoInterface
{
    /**
     * @return int
     */
    public function category(): int;
    
    
    /**
     * @return string[]
     */
    public function description(): array;
    
    
    /**
     * @return string[]
     */
    public function name(): array;
    
    
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
}