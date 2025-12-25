<?php
/* --------------------------------------------------------------
  PurposeDeleteRepositoryInterface.php 2020-01-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeDeleteRepositoryInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeDeleteRepositoryInterface
{
    /**
     * @param int $purposeId
     */
    public function deleteByPurposeId(int $purposeId): void;
    
    
    /**
     * @param string $alias
     */
    public function deleteByPurposeAlias(string $alias): void;
}