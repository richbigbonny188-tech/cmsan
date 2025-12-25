<?php
/* --------------------------------------------------------------
   ListingCategory.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\ValueObjects;

use Gambio\Admin\Modules\Configuration\Model\Collections\Groups;
use Gambio\Admin\Modules\Configuration\Model\Entities\Category;
use JsonSerializable;

/**
 * Class ListingCategory
 *
 * @package Gambio\Admin\Modules\Configuration\Model\ValueObjects
 */
class ListingCategory implements JsonSerializable
{
    /**
     * @var Category
     */
    private $details;
    
    /**
     * @var Groups
     */
    private $groups;
    
    
    /**
     * ListingCategory constructor.
     *
     * @param Category $details
     * @param Groups   $groups
     */
    private function __construct(Category $details, Groups $groups)
    {
        $this->details = $details;
        $this->groups  = $groups;
    }
    
    
    /**
     * @param Category $details
     * @param Groups   $groups
     *
     * @return ListingCategory
     */
    public static function create(Category $details, Groups $groups): ListingCategory
    {
        return new self($details, $groups);
    }
    
    
    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id'     => $this->details->id(),
            'label'  => $this->details->label(),
            'groups' => $this->groups,
            'tags'   => $this->groups->tags(),
        ];
    }
}