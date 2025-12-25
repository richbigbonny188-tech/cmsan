<?php
/*--------------------------------------------------------------
   WidgetNames.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections;

use Gambio\Admin\Modules\StatisticsOverview\Model\Features\TitleCollection;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetName;

/**
 * Class representing a collection of multilingual titles for a widget.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections
 */
class WidgetNames extends TitleCollection
{
    /**
     * Create instance.
     *
     * @param WidgetName ...$names Values.
     *
     * @return WidgetNames Instance.
     */
    public static function create(WidgetName ...$names): self
    {
        return new self($names);
    }
}