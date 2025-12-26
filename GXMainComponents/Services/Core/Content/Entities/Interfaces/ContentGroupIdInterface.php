<?php
/* --------------------------------------------------------------
   ContentGroupIdInterface.php 2019-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentGroupIdInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentGroupIdInterface
{
    /**
     * Returns the content group id if set, otherwise null will be returned.
     *
     * @return ContentIdentificationInterface|null
     */
    public function id() : ?ContentIdentificationInterface;
}