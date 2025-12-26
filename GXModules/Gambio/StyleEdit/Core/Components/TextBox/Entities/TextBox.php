<?php

/*--------------------------------------------------------------------------------------------------
    TextOption.php 2019-06-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\TextBox\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class TextOption
 * @package Gambio\StyleEdit\Core\Components\TextBox\Entities
 */
class TextBox extends AbstractComponentOption
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
    public function __toString()
    {
        return (string)$this->value();
    }
    
    
    /**
     * @return string|void|null
     */
    public function type(): string
    {
        return 'textbox';
    }
}
