<?php
/* --------------------------------------------------------------
   KlarnaHubTermsAndConditions.inc.php 2018-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class KlarnaHubTermsAndConditions
 *
 * Processes the Klarna terms and conditions content and applies the Klarna related texts.
 *
 * @package    GXModules
 * @subpackage GambioHub
 */
class KlarnaHubTermsAndConditions
{
	/**
	 * @var string
	 */
	protected $variableName = '{$klarna_hub_terms_and_conditions}';
	
	/**
	 * @var string
	 */
	protected $configurationKey = 'GAMBIO_HUB_KLARNA_HUB_TERMS_AND_CONDITIONS';
	
	/**
	 * @var string
	 */
	protected $defaultLanguageCode = 'en';
	
	/**
	 * @var string
	 */
	protected $contentBody;
	
	
	/**
	 * KlarnaHubTermsAndConditions constructor.
	 *
	 * @param string $contentBody Pre-rendered content body content that was not yet processed by this class.
	 */
	public function __construct($contentBody)
	{
		$this->contentBody = $contentBody;
	}
	
	
	/**
	 * Returns the processed content.
	 *
	 * @return string
	 */
	public function getContent()
	{
		$languageCode = $this->_getLanguageCode();
		
		$configurationValue = gm_get_conf($this->configurationKey);
		
		if(strpos($this->contentBody, $this->variableName) === false)
		{
			return $this->contentBody; // No need to process the content body. 
		}
		
		if(empty($configurationValue))
		{
			return str_replace($this->variableName, '', $this->contentBody); // Remove the parameter from the content.
		}
		
		$decodedConfigurationValue = json_decode($configurationValue, true);
		
		if(empty($decodedConfigurationValue))
		{
			return str_replace($this->variableName, '', $this->contentBody); // Remove the parameter from the content.
		}
		
		$termsAndConditions = array_key_exists($languageCode,
		                                       $decodedConfigurationValue) ? $decodedConfigurationValue[$languageCode] : $decodedConfigurationValue[$this->defaultLanguageCode];
		
		return str_replace($this->variableName, $termsAndConditions, $this->contentBody);
	}
	
	
	/**
	 * Get the code of the language to be used.
	 *
	 * There are multiple fallback levels in case no language code was stored in the current session.
	 *
	 * @return string
	 */
	protected function _getLanguageCode()
	{
		if(!empty($_SESSION['language_code']))
		{
			$languageCode = $_SESSION['language_code'];
		}
		elseif(defined(DEFAULT_LANGUAGE))
		{
			$languageCode = DEFAULT_LANGUAGE;
		}
		else
		{
			$languageCode = $this->defaultLanguageCode;
		}
		
		return $languageCode;
	}
}