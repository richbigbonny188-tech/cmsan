<?php
/*--------------------------------------------------------------------------------------------------
    OnModifierIdCreateListener.php 2020-06-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Properties\ProductModifiers\Database\Listeners;

use Gambio\Shop\ProductModifiers\Modifiers\Events\Interfaces\OnModifierIdCreateEventInterface;
use Gambio\Shop\Properties\ProductModifiers\Database\ValueObjects\PropertyModifierIdentifier;

class OnModifierIdCreateListener
{
    /**
     * @param OnModifierIdCreateEventInterface $event
     */
    public function __invoke(OnModifierIdCreateEventInterface $event)
    {
        if ($event->type() === 'property') {
            $event->stop();
            if((int)$event->id()) {
                $event->setModifierId(new PropertyModifierIdentifier((int)$event->id()));
            }
        }
    }
}