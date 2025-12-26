<?php
/* --------------------------------------------------------------
   ContentDeleterInterface.inc.php 2019-07-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentDeleterInterface
 */
interface ContentDeleterInterface
{
    /**
     * deletes the content data in database by id.
     *
     * @param ContentIdentificationInterface $Id
     *
     * @return $this|\ContentDeleterInterface Same instance for chained method calls.
     */
    public function deleteById(ContentIdentificationInterface $Id);
}