<?php
/*--------------------------------------------------------------------------------------------------
    PresentationInfoBuilderInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\Shop\ProductModifiers\Presentation\Core\Builder;


use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\ValueObjects\PresentationLabel;

/**
 * Interface PresentationInfoBuilderInterface
 * @package Gambio\Shop\ProductModifiers\Presentation\Core\Builder
 */
interface PresentationInfoBuilderInterface
{
    
    /**
     * @param PresentationLabel $label
     *
     * @return PresentationInfoBuilderInterface
     */
    public function withLabel(PresentationLabel $label) : PresentationInfoBuilderInterface;
    
    
    /**
     * @return PresentationInfoInterface
     */
    public function build(): PresentationInfoInterface;
}