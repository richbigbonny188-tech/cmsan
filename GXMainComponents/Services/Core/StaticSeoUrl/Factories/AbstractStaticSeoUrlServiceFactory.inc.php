<?php

/* --------------------------------------------------------------
   AbstractStaticSeoUrlServiceFactory.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Abstract Class AbstractStaticSeoUrlServiceFactory
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Factories
 */
abstract class AbstractStaticSeoUrlServiceFactory
{
    /**
     * Creates a staticSeoUrl read service.
     *
     * @return StaticSeoUrlReadServiceInterface
     */
    abstract public function createStaticSeoUrlReadService();
    
    
    /**
     * Creates a staticSeoUrl write service.
     *
     * @return StaticSeoUrlWriteServiceInterface
     */
    abstract public function createStaticSeoUrlWriteService();
}