<?php
/* --------------------------------------------------------------
   OptionDetailsUpdated.php 2021-03-31
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
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;

/**
 * Class OptionDetailsUpdated
 *
 * @package Gambio\Admin\Modules\Option\Model\Events
 * @codeCoverageIgnore
 */
class OptionDetailsUpdated
{
    /**
     * @var OptionId
     */
    private $id;
    
    /**
     * @var OptionDetails
     */
    private $optionDetails;
    
    
    /**
     * OptionDetailsUpdated constructor.
     *
     * @param OptionId      $id
     * @param OptionDetails $optionDetails
     */
    private function __construct(OptionId $id, OptionDetails $optionDetails)
    {
        $this->id            = $id;
        $this->optionDetails = $optionDetails;
    }
    
    
    /**
     * @param OptionId      $id
     * @param OptionDetails $optionDetails
     *
     * @return OptionDetailsUpdated
     */
    public static function create(OptionId $id, OptionDetails $optionDetails): OptionDetailsUpdated
    {
        return new self($id, $optionDetails);
    }
    
    
    /**
     * @return OptionId
     */
    public function id(): OptionId
    {
        return $this->id;
    }
    
    
    /**
     * @return OptionDetails
     */
    public function optionDetails(): OptionDetails
    {
        return $this->optionDetails;
    }
}