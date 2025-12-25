<?php
/*--------------------------------------------------------------------------------------------------
    OrdainedListItem.php 2019-09-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\OrdainedList\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;

/**
 * Class OrdainedListItem
 * @package Gambio\StyleEdit\Core\Components\OrdainedList\Entities
 */
class OrdainedListItemOption extends AbstractComponentOption
{
    
    /**
     * @param $value
     *
     * @return boolean
     */
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        return $value;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'ordainedlistitem';
    }
}