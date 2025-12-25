<?php
/* --------------------------------------------------------------
   OptionDeleted.php 2021-03-31
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
 * Class OptionDeleted
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionDeleted
{
    /**
     * @var OptionId
     */
    private $id;
    
    
    /**
     * OptionDeleted constructor.
     *
     * @param OptionId $id
     */
    private function __construct(OptionId $id)
    {
        $this->id = $id;
    }
    
    
    /**
     * @param OptionId $id
     *
     * @return OptionDeleted
     */
    public static function create(OptionId $id): OptionDeleted
    {
        return new self($id);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
}