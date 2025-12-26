<?php
/*--------------------------------------------------------------
   SentryBeforeSendCallback.php 2023-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App\Data;

use Sentry;

/**
 * Class SentryBeforeSendCallback
 */
class SentryBeforeSendCallback
{
    /**
     * Sentry's captureException function allows for EventHint instances to be passed on.
     * For an unknown reason the "extra" attribute is disregarded and not passed the Event class.
     *
     * This callback fixes just that.
     *
     * @param \Sentry\Event|null     $event
     * @param \Sentry\EventHint|null $hint
     *
     * @return \Sentry\Event|null
     */
    public function __invoke(?Sentry\Event $event, ?Sentry\EventHint $hint): ?Sentry\Event
    {
        if ($event !== null && $hint !== null) {
            
            if (empty($hint->extra) === false) {
                
                $event->setExtra($hint->extra);
            }
        }
        
        return $event;
    }
}