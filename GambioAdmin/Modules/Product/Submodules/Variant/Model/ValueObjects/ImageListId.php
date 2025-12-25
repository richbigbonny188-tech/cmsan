<?php
/*--------------------------------------------------------------
   ImageListId.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class ImageListId
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects
 */
class ImageListId
{
    /**
     * ImageListId constructor.
     *
     * @param int|null $imageListId
     */
    private function __construct(private ?int $imageListId)
    {
    }


    /**
     * @param int $id
     *
     * @return ImageListId
     */
    public static function createAsExisting(int $id): ImageListId
    {
        Assert::greaterThan($id, 0, 'The image list ID must be a positive integer. Got: %s');

        return new self($id);
    }


    /**
     * @return ImageListId
     */
    public static function createAsNonExistent(): ImageListId
    {
        return new self(null);
    }


    /**
     * @return int|null
     */
    public function value(): ?int
    {
        return $this->imageListId;
    }
}