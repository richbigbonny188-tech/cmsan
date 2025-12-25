<?php

/* --------------------------------------------------------------
   ContentServiceFactoryInterface.inc.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentServiceFactoryInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentServiceFactoryInterface
{
    
    /**
     * Return a content write service
     *
     * @return ContentWriteService
     */
    public function createWriteService(): ContentWriteService;
}
