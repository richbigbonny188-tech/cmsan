<?php
/*--------------------------------------------------------------------------------------------------
    ImageInfoBuilder.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\Builders;

use Gambio\Shop\ProductModifiers\Presentation\Core\Builder\AbstractPresentationInfoBuilder;
use Gambio\Shop\ProductModifiers\Presentation\Core\Builder\PresentationInfoBuilderInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationInfoInterface;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ImageInfo;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ValueObjects\ImagePath;

/**
 * Class SelectDropDownInfoBuilder
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\SelectDropDown\Builders
 */
class ImageInfoBuilder extends AbstractPresentationInfoBuilder
{
    /**
     * @var ImagePath
     */
    protected $path;
    
    
    /**
     * @inheritDoc
     */
    public function build(): PresentationInfoInterface
    {
        return new ImageInfo($this->label, $this->path);
    }
    
    
    /**
     * @param ImagePath $path
     *
     * @return PresentationInfoBuilderInterface
     */
    public function withPath(ImagePath $path): PresentationInfoBuilderInterface
    {
        $this->path = $path;
        
        return $this;
    }
}