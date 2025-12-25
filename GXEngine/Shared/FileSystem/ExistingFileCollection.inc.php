<?php
/* --------------------------------------------------------------
   ExistingFileCollection.inc.php 2017-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExistingFileCollection
 */
class ExistingFileCollection extends AbstractCollection
{
    /**
     * Valid type for the ExistingFileCollection is the ExistingFile.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\ExistingFile';
    }
}