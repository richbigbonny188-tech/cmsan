<?php
/* --------------------------------------------------------------
   InvoiceServiceSettingsInterface.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InvoiceServiceSettingsInterface
 *
 * This class contains the file system path to the invoice directory by using specific constants which are defined in
 * the config. It is used by the factory to build the proper service environment.
 * By encapsulating this dependency the code becomes more explicit and testable.
 *
 * @category   System
 * @package    Invoice
 * @subpackage Interfaces
 */
interface InvoiceServiceSettingsInterface
{
    /**
     * Returns the path to invoice directory.
     *
     * @return string
     */
    public function getInvoicesDirPath();
}