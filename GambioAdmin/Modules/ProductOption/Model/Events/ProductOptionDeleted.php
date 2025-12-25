<?php
/*--------------------------------------------------------------
   ProductOptionDeleted.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductOptionDeleted
 *
 * @package Gambio\Admin\Modules\ProductOption\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *               submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *               \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionDeleted
 */
class ProductOptionDeleted
{
    /**
     * @var AdditionalOptionId
     */
    private $id;
    
    
    /**
     * ProductOptionDeleted constructor.
     *
     * @param AdditionalOptionId $id
     */
    private function __construct(AdditionalOptionId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param AdditionalOptionId $id
     *
     * @return ProductOptionDeleted
     */
    public static function create(AdditionalOptionId $id): ProductOptionDeleted
    {
        return new self($id);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function id(): AdditionalOptionId
    {
        return $this->id;
    }
}