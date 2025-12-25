<?php
/*--------------------------------------------------------------------------------------------------
    ImageInfo.php 2020-02-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Presentation\Implementations\Image;

use Gambio\Shop\ProductModifiers\Presentation\Core\AbstractPresentationInfo;
use Gambio\Shop\ProductModifiers\Presentation\Core\PresentationTypeInterface;
use Gambio\Shop\ProductModifiers\Presentation\Core\ValueObjects\PresentationLabel;
use Gambio\Shop\ProductModifiers\Presentation\Implementations\Image\ValueObjects\ImagePath;

/**
 * Class ImageInfo
 * @package Gambio\Shop\ProductModifiers\Presentation\Implementations\Image
 */
class ImageInfo extends AbstractPresentationInfo
{
    /**
     * @var ImagePath
     */
    protected $path;
    
    
    /**
     * AbstractPresentationInfo constructor.
     *
     * @param PresentationLabel $label
     * @param ImagePath         $path
     */
    public function __construct(PresentationLabel $label, ImagePath $path)
    {
        parent::__construct($label);
        $this->path = $path;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): PresentationTypeInterface
    {
        return ImageType::instance();
    }
    
    
    /**
     * @return ImagePath
     */
    public function path(): ImagePath
    {
        return $this->path;
    }
}