<?php
/* --------------------------------------------------------------
   AgreementCustomerInterface.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementCustomerInterface
 *
 * @category   System
 * @package    Agreement
 * @subpackage ValueObjects
 */
interface AgreementCustomerInterface
{
    
    /**
     * Returns the customers name.
     *
     * @return string Customer name.
     */
    public function getCustomerName();
    
    
    /**
     * Returns the customers email.
     *
     * @return string Customer email.
     */
    public function getCustomerEmail();
}