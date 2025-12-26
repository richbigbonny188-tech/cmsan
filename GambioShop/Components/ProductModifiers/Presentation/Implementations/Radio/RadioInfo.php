<?php
/*--------------------------------------------------------------------------------------------------
    RadioInfo.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\Radio;

use Gambio\Shop\ProductModifiers\Presentation\Core\AbstractPresentationInfo;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Class SelectDropDownInfo
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\SelectDropDown
 */
class RadioInfo extends AbstractPresentationInfo
{
    
    /**
     * @inheritDoc
     */
    public function type(): PresentationTypeInterface
    {
        return RadioType::instance();
    }
}