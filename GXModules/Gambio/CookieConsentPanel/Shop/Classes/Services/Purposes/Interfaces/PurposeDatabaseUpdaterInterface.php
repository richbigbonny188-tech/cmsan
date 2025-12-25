<?php
/* --------------------------------------------------------------
  PurposeDatabaseUpdaterInterface.php 2020-01-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeDatabaseUpdaterInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeDatabaseUpdaterInterface
{
    /**
     * @param PurposeUpdateDtoInterface $dto
     */
    public function update(PurposeUpdateDtoInterface $dto): void;
    
    
    /**
     * @param int  $id
     * @param bool $status
     *
     * @return mixed
     */
    public function updateStatus(int $id, bool $status);
}