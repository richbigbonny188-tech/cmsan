<?php
/* --------------------------------------------------------------
   KlarnaHubException.inc.php 2017-10-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubException
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubException extends Exception
{
	/**
	 * @var array
	 */
	protected $errorResponse;
	
	
	/**
	 * Construct the exception.
	 *
	 * @param array     $errorResponse Klarna API error response (associative array).
	 * @param int       $code          Exception code.
	 * @param Exception $previous      Previous throwable used for the exception chaining.
	 */
	public function __construct(array $errorResponse, $code = 0, Exception $previous = null)
	{
		parent::__construct($errorResponse['error_messages'][0], $code, $previous);
		
		$this->errorResponse = $errorResponse;
	}
	
	
	/**
	 * Returns the Klarna API error response.
	 *
	 * @return array
	 */
	public function getErrorResponse()
	{
		return $this->errorResponse;
	}
}