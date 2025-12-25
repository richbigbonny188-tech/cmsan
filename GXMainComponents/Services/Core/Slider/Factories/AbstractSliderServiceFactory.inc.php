<?php

/* --------------------------------------------------------------
   AbstractSliderServiceFactory.inc.php 2016-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Abstract Class AbstractSliderServiceFactory
 *
 * @category   System
 * @package    Slider
 * @subpackage Factories
 */
abstract class AbstractSliderServiceFactory
{
    /**
     * Creates a slider read service.
     *
     * @return SliderReadServiceInterface
     */
    abstract public function createSliderReadService();
    
    
    /**
     * Creates a slider write service.
     *
     * @return SliderWriteServiceInterface
     */
    abstract public function createSliderWriteService();
}