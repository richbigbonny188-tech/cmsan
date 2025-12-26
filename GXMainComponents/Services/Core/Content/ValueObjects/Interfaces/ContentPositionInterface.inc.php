<?php

/* --------------------------------------------------------------
   ContentPositionInterface.inc.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentPositionInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentPositionInterface
{
    /**
     * Return the position
     *
     * @return string
     */
    public function position(): string;
}
