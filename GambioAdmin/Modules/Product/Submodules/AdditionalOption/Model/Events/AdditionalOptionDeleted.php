<?php
/*--------------------------------------------------------------
   AdditionalOptionDeleted.php 2023-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;

/**
 * Class AdditionalOptionDeleted
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events
 */
class AdditionalOptionDeleted
{
    /**
     * AdditionalOptionDeleted constructor.
     *
     * @param AdditionalOptionId $id
     */
    private function __construct(private AdditionalOptionId $id) { }
    
    
    /**
     * @param AdditionalOptionId $id
     *
     * @return AdditionalOptionDeleted
     */
    public static function create(AdditionalOptionId $id): AdditionalOptionDeleted
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