<?php

/* --------------------------------------------------------------
   ContentNameAttributeInterface.inc.php 2019-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentNameAttributeInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentNameAttributeInterface
{
    /**
     * Return the localized content names
     *
     * @return ContentNameCollection
     */
    public function names(): ?ContentNameCollection;
}
