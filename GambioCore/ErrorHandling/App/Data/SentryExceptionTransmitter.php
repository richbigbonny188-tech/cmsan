<?php
/*--------------------------------------------------------------
   SentryExceptionTransmitter.php 2023-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App\Data;

use Exception;

/**
 * Class SentryExceptionTransmitter
 *
 * @package Gambio\Core\ErrorHandling\App\Data
 * @codeCoverageIgnore
 */
class SentryExceptionTransmitter
{
    public function __construct(private SentryEventHintFactory $factory) { }
    
    
    /**
     * @param Exception $exception
     * @param array     $context
     *
     * @return void
     */
    public function transmit(Exception $exception, array $context = []): void
    {
        \Sentry\captureException($exception, empty($context) ? null : $this->factory->createEventHint($context));
    }
}