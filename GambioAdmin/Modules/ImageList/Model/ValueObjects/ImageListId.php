<?php
/*--------------------------------------------------------------
   ImageListId.php 2021-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ImageListId
 * @package Gambio\Admin\Modules\ImageList\Model\ValueObjects
 */
class ImageListId
{
    /**
     * @var int
     */
    private $imageListId;
    
    
    /**
     * ImageListId constructor.
     *
     * @param int $imageListId
     */
    private function __construct(int $imageListId)
    {
        $this->imageListId = $imageListId;
    }
    
    
    /**
     * @param int $imageListId
     *
     * @return ImageListId
     */
    public static function create(int $imageListId): ImageListId
    {
        Assert::greaterThan($imageListId, 0, 'The image list ID must be a positive integer. Got: %s');
        
        return new self($imageListId);
    }
    
    
    /**
     * @return int
     */
    public function value(): int
    {
        return $this->imageListId;
    }
}