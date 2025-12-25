<?php
/*--------------------------------------------------------------
   TransmittableExceptionHandler.php 2023-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\Services;

use Exception;
use stdClass;

/**
 * Interface TransmittableExceptionHandler
 *
 * @package Gambio\Core\ErrorHandling\Services
 */
interface ExceptionTransmitter
{
    /**
     * Transmits provided Exception
     *
     * @param Exception $exception
     * @param array     $context
     *
     * @return void
     */
    public function handleException(
        Exception $exception,
        array $context = []
    ): void;
}