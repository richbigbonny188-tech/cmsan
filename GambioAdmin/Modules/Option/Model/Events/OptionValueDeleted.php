<?php
/* --------------------------------------------------------------
   OptionValueDeleted.php 2021-03-31
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
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;

/**
 * Class OptionValueDeleted
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionValueDeleted
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionValueId
     */
    private $optionValueId;
    
    
    /**
     * OptionValueDeleted constructor.
     *
     * @param OptionId      $id
     * @param OptionValueId $optionValueId
     */
    private function __construct(OptionId $id, OptionValueId $optionValueId)
    {
        $this->id            = $id;
        $this->optionValueId = $optionValueId;
    }
    
    
    /**
     * @param OptionId      $id
     * @param OptionValueId $optionValueId
     *
     * @return OptionValueDeleted
     */
    public static function create(OptionId $id, OptionValueId $optionValueId): OptionValueDeleted
    {
        return new self($id, $optionValueId);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return OptionValueId
     */
    public function optionValueId(): OptionValueId
    {
        return $this->optionValueId;
    }
}