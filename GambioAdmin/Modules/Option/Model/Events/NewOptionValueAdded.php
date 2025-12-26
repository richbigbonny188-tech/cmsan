<?php
/* --------------------------------------------------------------
   NewOptionValueAdded.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Events;

use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\ValueObjects\NewOptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;

/**
 * Class NewOptionValueAdded
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class NewOptionValueAdded
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionDetails
     */
    private $newOptionValue;
    
    
    /**
     * OptionDetailsUpdated constructor.
     *
     * @param OptionId       $id
     * @param NewOptionValue $newOptionValue
     */
    private function __construct(OptionId $id, NewOptionValue $newOptionValue)
    {
        $this->id             = $id;
        $this->newOptionValue = $newOptionValue;
    }
    
    
    /**
     * @param OptionId       $id
     * @param NewOptionValue $newOptionValue
     *
     * @return NewOptionValueAdded
     */
    public static function create(OptionId $id, NewOptionValue $newOptionValue): NewOptionValueAdded
    {
        return new self($id, $newOptionValue);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return NewOptionValue
     */
    public function newOptionValue(): NewOptionValue
    {
        return $this->newOptionValue;
    }
}