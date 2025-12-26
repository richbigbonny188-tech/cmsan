<?php

/*--------------------------------------------------------------------------------------------------
    NumberBoxOption.php 2019-06-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\NumberBox\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Exception;

/**
 * Class NumberBoxOption
 * @package Gambio\StyleEdit\Core\Components\TextBox\Entities
 */
class NumberBoxOption extends AbstractComponentOption
{
    /**
     * Returns preg_match pattern string.
     *
     * @return string
     */
    protected function getPattern(): string
    {
        return '/^\d+.?(\d+)?(px|em|ex|%|in|cm|mm|pt|pc|ch|rem|vw|vh|vmin|vmax)$/';
    }
    
    
    /**
     * Validates the value.
     *
     * @param $value
     *
     * @return boolean
     */
    public function isValid($value): bool
    {
        $value = preg_replace('/\s+/', '', $value);
        
        return (bool)preg_match($this->getPattern(), $value);
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
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        // @todo implement the initialize.
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'numberbox';
    }
}
