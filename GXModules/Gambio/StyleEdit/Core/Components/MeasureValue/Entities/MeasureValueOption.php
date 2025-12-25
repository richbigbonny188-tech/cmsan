<?php

/*--------------------------------------------------------------------------------------------------
    MarginOption.php 2019-06-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\MeasureValue\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class MarginOption
 * @package Gambio\StyleEdit\Core\Components\MarginOption\Entities
 */
class MeasureValueOption extends AbstractComponentOption
{
    /**
     * Validates the value.
     *
     * @param $value
     *
     * @return boolean
     */
    public function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * Parses value.
     *
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
        return 'measure-value';
    }
}
