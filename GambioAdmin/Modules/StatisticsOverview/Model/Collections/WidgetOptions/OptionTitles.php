<?php
/*--------------------------------------------------------------
   OptionTitles.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;

use Gambio\Admin\Modules\StatisticsOverview\Model\Features\TitleCollection;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionTitle;

/**
 * Class representing a collection of titles for an option.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions
 */
class OptionTitles extends TitleCollection
{
    /**
     * Create instance.
     *
     * @param OptionTitle ...$titles Values.
     *
     * @return OptionTitles Instance.
     */
    public static function create(OptionTitle ...$titles): self
    {
        return new self($titles);
    }
}