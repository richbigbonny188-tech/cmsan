<?php
/* --------------------------------------------------------------
  PurposeWriteServiceFactoryInterface.php 2020-01-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeWriteServiceFactoryInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeWriteServiceFactoryInterface
{
    /**
     * @return PurposeWriteServiceInterface
     */
    public function service(): PurposeWriteServiceInterface;
}