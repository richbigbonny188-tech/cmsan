<?php

/* --------------------------------------------------------------
   ConfigurationStep.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a configuration step value object
 */
class ConfigurationStep extends IntType
{
    /**
     * Maximum step count
     */
    const MAX_STEPS = 4;
    
    
    /**
     * Create an instance
     *
     * @param string $step Step number as string
     *
     * @throws RangeException Step exceeded maximum count
     */
    public function __construct($step)
    {
        $valueAsInteger = (int)$step;
        
        if ($valueAsInteger > self::MAX_STEPS) {
            throw new RangeException('Maximum amount of steps reached');
        }
        
        parent::__construct($step);
    }
}