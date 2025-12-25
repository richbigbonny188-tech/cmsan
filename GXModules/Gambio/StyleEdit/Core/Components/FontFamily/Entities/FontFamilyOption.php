<?php
/*--------------------------------------------------------------------------------------------------
    FontFamilyOption.php 2019-6-13
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\FontFamily\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class FontFamilyOption
 * @package Gambio\StyleEdit\Core\Components\FontFamily\Entities
 */
class FontFamilyOption extends AbstractComponentOption
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
        return 'fontfamily';
    }
}
