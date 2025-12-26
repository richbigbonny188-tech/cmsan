<?php
/*--------------------------------------------------------------------------------------------------
    BoxedTextInfoBuilder.php 2020-02-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\SquareText\Builders;


use Gambio\Shop\ProductModifiers\Presentation\Core\Builder\AbstractPresentationInfoBuilder;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\SquareText\BoxedTextInfo;

/**
 * Class BoxedTextInfoBuilder
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\Text\Builders
 */
class BoxedTextInfoBuilder extends AbstractPresentationInfoBuilder
{
    /**
     * @inheritDoc
     */
    public function build(): PresentationInfoInterface
    {
        return new BoxedTextInfo($this->label);
    }
}