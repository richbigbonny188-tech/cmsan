<?php
/*--------------------------------------------------------------
   WidgetOption.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionId;

/**
 * Interface representing widget options.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects
 */
interface WidgetOption
{
    /**
     * Return ID.
     *
     * @return OptionId ID.
     */
    public function id(): OptionId;
    
    
    /**
     * Return value.
     */
    public function value();
    
    
    /**
     * Return type.
     *
     * @return string Type.
     */
    public function type(): string;
    
    
    /**
     * Return multilingual titles.
     *
     * @return OptionTitles Multilingual titles.
     */
    public function titles(): OptionTitles;
}