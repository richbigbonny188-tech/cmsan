<?php
/* --------------------------------------------------------------
   HubTransactionsApiClientInterface.inc.php 2017-02-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use \HubPublic\ValueObjects\CartContent;
use \HubPublic\ValueObjects\ClientSessionInformation;
use \HubPublic\ValueObjects\CustomerInformation;
use \HubPublic\ValueObjects\HubClientInformation;
use \HubPublic\ValueObjects\HubTransactionCode;
use \HubPublic\ValueObjects\OrderContent;

/**
 * Interface HubTransactionsApiClientInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubTransactionsApiClientInterface
{
	/**
	 * Returns an array of allowed payment modules for the respective client.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $unallowedModuleCodes     Array of unallowed module
	 *                                                                                   codes.
	 *
	 * @return array Returns an array with the available modules information.
	 */
	public function getAllowedPaymentModules(CartContent $cartContent,
	                                         CustomerInformation $customerInformation,
	                                         HubClientInformation $hubClientInformation,
	                                         ClientSessionInformation $clientSessionInformation,
	                                         array $unallowedModuleCodes);
	
	
	/**
	 * Starts an transaction and returns the transaction code.
	 *
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\OrderContent             $orderContent             Order content.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 *
	 * @return string Returns the transaction code.
	 */
	public function startTransaction(HubClientInformation $hubClientInformation,
	                                 OrderContent $orderContent,
	                                 ClientSessionInformation $clientSessionInformation);
	
	
	/**
	 * Returns the transaction details.
	 *
	 * @param \HubPublic\ValueObjects\HubTransactionCode $transactionCode Transaction code.
	 *
	 * @return array Returns the transaction details.
	 */
	public function getTransactionDetails(HubTransactionCode $transactionCode);
	
	
	/**
	 * Returns a string containing html or nothing if payment module has no extra page before confirmation.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $getData                  GET-Request data.
	 * @param array                                            $postData                 POST-Request data.
	 * @param string                                           $moduleCode               Module Code.
	 *
	 * @return string Returns a string containing html or nothing.
	 */
	public function getBeforeTransactionPageContent(CartContent $cartContent,
	                                                CustomerInformation $customerInformation,
	                                                HubClientInformation $hubClientInformation,
	                                                ClientSessionInformation $clientSessionInformation,
	                                                array $getData,
	                                                array $postData,
	                                                $moduleCode);
	
	
	/**
	 * Returns an array of confirmation contents served by the selected payment module.
	 *
	 * @param \HubPublic\ValueObjects\CartContent              $cartContent              Cart content.
	 * @param \HubPublic\ValueObjects\CustomerInformation      $customerInformation      Customer information.
	 * @param \HubPublic\ValueObjects\HubClientInformation     $hubClientInformation     Hub client information.
	 * @param \HubPublic\ValueObjects\ClientSessionInformation $clientSessionInformation Session information.
	 * @param array                                            $getData                  GET-Request data.
	 * @param array                                            $postData                 POST-Request data.
	 * @param string                                           $moduleCode               Module Code.
	 *
	 * @return string Returns an confirmation contents array.
	 */
	public function getConfirmationContents(CartContent $cartContent,
	                                        CustomerInformation $customerInformation,
	                                        HubClientInformation $hubClientInformation,
	                                        ClientSessionInformation $clientSessionInformation,
	                                        array $getData,
	                                        array $postData,
	                                        $moduleCode);
}