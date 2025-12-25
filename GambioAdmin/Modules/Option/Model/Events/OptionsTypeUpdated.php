<?php
/* --------------------------------------------------------------
   OptionsTypeUpdated.php 2021-03-31
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
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;

/**
 * Class OptionsTypeUpdated
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionsTypeUpdated
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionType
     */
    private $type;
    
    
    /**
     * OptionsTypeUpdated constructor.
     *
     * @param OptionId   $id
     * @param OptionType $type
     */
    private function __construct(OptionId $id, OptionType $type)
    {
        $this->id   = $id;
        $this->type = $type;
    }
    
    
    /**
     * @param OptionId   $id
     * @param OptionType $type
     *
     * @return OptionsTypeUpdated
     */
    public static function create(OptionId $id, OptionType $type): OptionsTypeUpdated
    {
        return new self($id, $type);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return OptionType
     */
    public function type(): OptionType
    {
        return $this->type;
    }
}