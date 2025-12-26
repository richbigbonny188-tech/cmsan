<?php
/* --------------------------------------------------------------
   EnvPackingSlipServiceSettings.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvPackingSlipServiceSettings
 *
 * This class contains the file system path to the packing slip directory by using specific constants which are defined
 * in the config. It is used by the factory to build the proper service environment. By encapsulating this dependency
 * the code becomes more explicit and testable.
 *
 * @category   Core
 * @package    PackingSlip
 * @subpackage ValueObjects
 */
class EnvPackingSlipServiceSettings
{
    /**
     * Returns the path to packing slip directory.
     *
     * @return string
     */
    public function getPackingSlipDirPath()
    {
        return DIR_FS_CATALOG . 'export/packingslip/';
    }
}