<?php
/* --------------------------------------------------------------
   ReviewCollection.inc.php 2017-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewCollection
 *
 * @category   System
 * @package    Review
 * @subpackage Entities
 */
class ReviewCollection extends EditableCollection
{
    /**
     * Valid type for ReviewCollection is ReviewInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\ReviewInterface';
    }
}