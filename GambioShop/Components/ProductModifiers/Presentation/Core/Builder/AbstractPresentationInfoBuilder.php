<?php
/*--------------------------------------------------------------------------------------------------
    AbstractPresentationInfoBuilder.php 2020-01-23
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
 * Class AbstractPresentationInfoBuilder
 * @package Gambio\Shop\ProductModifiers\Presentation\Core\Builder
 */
abstract class AbstractPresentationInfoBuilder implements PresentationInfoBuilderInterface
{
    /**
     * @var PresentationLabel
     */
    protected $label;
    
    
    /**
     * @inheritDoc
     */
    public function withLabel(PresentationLabel $label): PresentationInfoBuilderInterface
    {
        $this->label = $label;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    abstract public function build(): PresentationInfoInterface;
}