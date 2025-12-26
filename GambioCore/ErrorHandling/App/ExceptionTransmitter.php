<?php
/*--------------------------------------------------------------
   TransmittableExceptionHandler.php 2023-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App;

use Exception;
use Gambio\Core\ErrorHandling\App\Data\SentryExceptionTransmitter;
use Gambio\Core\ErrorHandling\App\Data\TransmissionCache;
use Gambio\Core\ErrorHandling\App\Data\TransmissionConsentStorage;
use Gambio\Core\ErrorHandling\Services\ExceptionTransmitter as ExceptionTransmitterInterface;

/**
 * Class TransmittableExceptionHandler
 *
 * Can transmit Exception data to external services
 *
 * @package  Gambio\Core\ErrorHandling\App
 * @internal do not send every Exception
 */
class ExceptionTransmitter implements ExceptionTransmitterInterface
{
    /**
     * TransmittableExceptionHandler constructor.
     *
     * @param TransmissionConsentStorage $storage
     * @param TransmissionCache          $cache
     * @param SentryExceptionTransmitter $sentry
     */
    public function __construct(
        private TransmissionConsentStorage $storage,
        private TransmissionCache          $cache,
        private SentryExceptionTransmitter $sentry
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function handleException(
        Exception $exception,
        array     $context = []
    ): void {
        
        if ($this->storage->consentedToDataTransmission() === false || $this->cache->wasRecentlyTransmitted($exception)) {
            
            return;
        }
        
        $this->transmit($exception, $context);
    }
    
    
    /**
     * @param Exception $exception
     * @param array     $context
     *
     * @return void
     */
    private function transmit(Exception $exception, array $context = []): void
    {
        $this->sentry->transmit($exception, $context);
        
        $this->cache->add($exception);
    }
}