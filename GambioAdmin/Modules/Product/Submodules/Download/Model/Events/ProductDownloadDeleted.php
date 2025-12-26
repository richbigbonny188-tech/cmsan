<?php
/*--------------------------------------------------------------
   ProductDownloadDeleted.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductDownloadDeleted
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Events
 */
class ProductDownloadDeleted
{
    /**
     * ProductDownloadDeleted constructor.
     *
     * @param AdditionalOptionId $id
     */
    private function __construct(private AdditionalOptionId $id)
    {
    }


    /**
     * @param AdditionalOptionId $id
     *
     * @return ProductDownloadDeleted
     */
    public static function create(AdditionalOptionId $id): ProductDownloadDeleted
    {
        return new static($id);
    }


    /**
     * @return AdditionalOptionId
     */
    public function id(): AdditionalOptionId
    {
        return $this->id;
    }
}