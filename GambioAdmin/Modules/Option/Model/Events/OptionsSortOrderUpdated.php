<?php
/* --------------------------------------------------------------
   OptionsSortOrderUpdated.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Events;

use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;

/**
 * Class OptionsSortOrderUpdated
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionsSortOrderUpdated
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var int
     */
    private $sortingOrder;
    
    
    /**
     * OptionsSortOrderUpdated constructor.
     *
     * @param OptionId $id
     * @param int      $sortingOrder
     */
    private function __construct(OptionId $id, int $sortingOrder)
    {
        $this->id           = $id;
        $this->sortingOrder = $sortingOrder;
    }
    
    
    /**
     * @param OptionId $id
     * @param int      $sortingOrder
     *
     * @return OptionsSortOrderUpdated
     */
    public static function create(OptionId $id, int $sortingOrder): OptionsSortOrderUpdated
    {
        return new self($id, $sortingOrder);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return int
     */
    public function sortingOrder(): int
    {
        return $this->sortingOrder;
    }
}