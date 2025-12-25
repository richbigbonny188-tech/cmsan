<?php

/* --------------------------------------------------------------
   ContentTypeInterface.inc.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentTypeInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentTypeInterface
{
    /**
     * Return the content type
     *
     * @return string
     */
    public function type();
}
