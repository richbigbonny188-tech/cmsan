<?php
/*--------------------------------------------------------------
   OriginalImagesDirectory.php 2021-05-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Gambio\Core\Application\ValueObjects\Path;

/**
 * Class OriginalImagesDirectory
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class OriginalImagesDirectory
{
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * OriginalImagesDirectory constructor.
     *
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->path->base() . '/images/product_images/original_images';
    }
}