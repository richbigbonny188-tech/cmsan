<?php

/* --------------------------------------------------------------
   BuilderInterface.inc.php 2019-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface BuilderInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface BuilderInterface
{
    /**
     * Return new instance of the builder
     */
    public static function create();
    
    
    /**
     * Return the created instance
     */
    public function build();
}