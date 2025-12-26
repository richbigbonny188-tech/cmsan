<?php
/*--------------------------------------------------------------------------------------------------
    AbstractPresentationInfo.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Core;

use Gambio\Shop\ProductModifiers\Presentation\Core\ValueObjects\PresentationLabel;

/**
 * Class AbstractPresentationInfo
 * @package Gambio\Shop\ProductModifiers\Modifiers\Presentation
 */
abstract class AbstractPresentationInfo implements PresentationInfoInterface
{
    /**
     * @var PresentationLabel
     */
    private $label;
    
    
    /**
     * AbstractPresentationInfo constructor.
     *
     * @param PresentationLabel $label
     */
    public function __construct(PresentationLabel $label)
    {
        $this->label = $label;
    }
    
    
    /**
     * @return PresentationLabel
     */
    public function label(): PresentationLabel
    {
        return $this->label;
    }
    
    
}