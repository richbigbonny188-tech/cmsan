<?php
/*--------------------------------------------------------------------------------------------------
    TextType.php 2020-02-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\Text;

use Gambio\Shop\ProductModifiers\Presentation\Core\AbstractPresentationType;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;

/**
 * Class TextType
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\Text
 */
class TextType extends AbstractPresentationType
{
    /**
     * @var string
     */
    protected static $TYPE = 'Text';
    /**
     * @var PresentationTypeInterface
     */
    protected static $instance;
}