<?php
/* --------------------------------------------------------------
   CustomerCountryCollection.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerCountryCollection
 *
 * This class is a container (collection) for CustomerCountry objects.
 *
 * @category   System
 * @package    Category
 * @subpackage Collections
 */
class CustomerCountryCollection extends AbstractCollection
{
    /**
     * Get valid type.
     *
     * @return string Valid type.
     */
    protected function _getValidType()
    {
        return 'CustomerCountry';
    }
}