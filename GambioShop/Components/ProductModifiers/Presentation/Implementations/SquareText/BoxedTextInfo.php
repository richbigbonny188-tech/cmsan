<?php
/*--------------------------------------------------------------------------------------------------
    BoxedTextInfo.php 2020-02-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\SquareText;

use Gambio\Shop\ProductModifiers\Presentation\Core\AbstractPresentationInfo;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Class BoxedTextInfo
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\Text
 */
class BoxedTextInfo extends AbstractPresentationInfo
{
    
    /**
     * @inheritDoc
     */
    public function type(): PresentationTypeInterface
    {
        return BoxedTextType::instance();
    }
}