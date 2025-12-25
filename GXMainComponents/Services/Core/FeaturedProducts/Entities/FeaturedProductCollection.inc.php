<?php
/* --------------------------------------------------------------
   FeaturedProductCollection.inc.php 2019-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeaturedProductCollection extends EditableCollection
{
    /**
     * Valid type for FeaturedProductCollection is FeaturedProductInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return \FeaturedProductInterface::class;
    }
}