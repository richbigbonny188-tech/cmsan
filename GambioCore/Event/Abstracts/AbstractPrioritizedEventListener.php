<?php
/* --------------------------------------------------------------
   PrioritizedEventListener.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Event\Abstracts;

use Gambio\Core\Event\PrioritizedEventListener;

/**
 * Class PrioritizedEventListener
 *
 * @package Gambio\Core\Event\Abstracts
 * @codeCoverageIgnore
 */
class AbstractPrioritizedEventListener implements PrioritizedEventListener
{
    /**
     * @inheritDoc
     */
    public function priority(): int
    {
        return PrioritizedEventListener::PRIORITY_NORMAL;
    }
}