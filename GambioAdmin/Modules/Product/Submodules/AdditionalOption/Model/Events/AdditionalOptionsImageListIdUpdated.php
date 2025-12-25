<?php
/*--------------------------------------------------------------------
 AdditionalOptionsImageListIdUpdated.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;

/**
 * Class AdditionalOptionsImageListIdUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events
 */
class AdditionalOptionsImageListIdUpdated
{
    /**
     * AdditionalOptionsImageListIdUpdated constructor.
     *
     * @param AdditionalOptionId $additionalOptionId
     * @param ImageListId        $imageListId
     */
    private function __construct(
        private AdditionalOptionId $additionalOptionId,
        private ImageListId        $imageListId
    ) {
    }
    
    
    /**
     * @param AdditionalOptionId $additionalOptionId
     * @param ImageListId        $imageListId
     *
     * @return AdditionalOptionsImageListIdUpdated
     */
    public static function create(
        AdditionalOptionId $additionalOptionId,
        ImageListId        $imageListId
    ): AdditionalOptionsImageListIdUpdated {
        return new static($additionalOptionId, $imageListId);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function additionalOptionId(): AdditionalOptionId
    {
        return $this->additionalOptionId;
    }
    
    
    /**
     * @return ImageListId
     */
    public function imageListId(): ImageListId
    {
        return $this->imageListId;
    }
}