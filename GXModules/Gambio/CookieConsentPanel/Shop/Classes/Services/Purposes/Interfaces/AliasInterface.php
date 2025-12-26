<?php
/* --------------------------------------------------------------
  AliasInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface AliasInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface AliasInterface
{
    /**
     * @return string
     */
    public function value(): ?string;
}