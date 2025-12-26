<?php
/* --------------------------------------------------------------
   HubExceptionErrorLevelInterface.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions\Interfaces;

/**
 * Interface HubExceptionErrorLevelInterface
 *
 * @package HubPublic\Exceptions\Interfaces
 */
interface HubExceptionErrorLevelInterface
{
    /**
     * Returns the exception error level.
     *
     * @return string Error level.
     */
    public function getErrorLevel(): string;
}
