<?php
/*--------------------------------------------------------------------------------------------------
    SelectDropDownInfoBuilder.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\SelectDropDown\Builders;


use Gambio\Shop\ProductModifiers\Presentation\Core\Builder\AbstractPresentationInfoBuilder;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\SelectDropDown\SelectDropDownInfo;

/**
 * Class SelectDropDownInfoBuilder
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\SelectDropDown\Builders
 */
class SelectDropDownInfoBuilder extends AbstractPresentationInfoBuilder
{
    /**
     * @inheritDoc
     */
    public function build(): PresentationInfoInterface
    {
        return new SelectDropDownInfo($this->label);
    }
}