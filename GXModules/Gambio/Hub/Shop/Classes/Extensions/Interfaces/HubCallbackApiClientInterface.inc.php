<?php
/* --------------------------------------------------------------
   HubCallbackApiClientInterface.inc.php 2017-04-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface HubCallbackApiClientInterface
 *
 * @category   System
 * @package    Extensions
 * @subpackage GambioHub
 */
interface HubCallbackApiClientInterface
{
	/**
	 * Executes Gambio Hub payment module callback.
	 *
	 * @param string $paymentModuleCode Gambio Hub Payment Module Code
	 * @param bool   $isPostRequest     Flag, if url will be executed via GET or POST
	 * @param array  $getData           GET data as an array
	 * @param array  $postData          POST data as an array
	 * @param array  $headers           Headers as an array like ['X-Custom-Header: Foo']
	 *
	 * @return \HubPublic\ValueObjects\HttpResponse Returns the HTTP response
	 */
	public function execute($paymentModuleCode,
	                        $isPostRequest = false,
	                        array $getData = [],
	                        array $postData = [],
	                        array $headers = []);
}