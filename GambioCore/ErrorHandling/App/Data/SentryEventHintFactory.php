<?php
/*--------------------------------------------------------------
   SentryEventHintFactory.php 2023-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling\App\Data;

use Sentry\EventHint;

/**
 * Class SentryEventHintFactory
 *
 * @package Gambio\Core\ErrorHandling\App\Data
 */
class SentryEventHintFactory
{
    /**
     * @param array $context
     *
     * @return EventHint
     */
    public function createEventHint(array $context): EventHint
    {
        return EventHint::fromArray(['extra' => $context]);
    }
}