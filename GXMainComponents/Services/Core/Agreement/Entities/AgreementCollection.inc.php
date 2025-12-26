<?php
/* --------------------------------------------------------------
   AgreementCollection.inc.php 2017-05-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AgreementCollection
 *
 * @category   System
 * @package    Agreement
 * @subpackage Entities
 */
class AgreementCollection extends EditableCollection
{
    /**
     * Valid type for the AgreementCollection is the AgreementInterface.
     *
     * @return string
     */
    public function _getValidType()
    {
        return '\AgreementInterface';
    }
}