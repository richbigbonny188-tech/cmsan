<?php
/* --------------------------------------------------------------
   OptionValueUpdated.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Events;

use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;

/**
 * Class OptionValueUpdated
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionValueUpdated
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionValue
     */
    private $optionValue;
    
    
    /**
     * OptionValueUpdated constructor.
     *
     * @param OptionId    $id
     * @param OptionValue $optionValue
     */
    private function __construct(OptionId $id, OptionValue $optionValue)
    {
        $this->id          = $id;
        $this->optionValue = $optionValue;
    }
    
    
    /**
     * @param OptionId    $id
     * @param OptionValue $optionValue
     *
     * @return OptionValueUpdated
     */
    public static function create(OptionId $id, OptionValue $optionValue): OptionValueUpdated
    {
        return new self($id, $optionValue);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return OptionValue
     */
    public function optionValue(): OptionValue
    {
        return $this->optionValue;
    }
}