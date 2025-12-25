<?php
/*--------------------------------------------------------------------------------------------------
    PresentationInfoInterface.php 2020-01-23
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
 * Interface PresentationInfoInterface
 * @package Gambio\Shop\ProductModifiers\Modifiers\Presentation
 */
interface PresentationInfoInterface
{
    
    /**
     * Information for serialization/deserialization purposes
     * @return PresentationTypeInterface
     */
    public function type(): PresentationTypeInterface;
    
    
    /**
     * @return PresentationLabel
     */
    public function label(): PresentationLabel;
    
}