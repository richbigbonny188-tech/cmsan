<?php
/* --------------------------------------------------------------
   HubAssetHelper.inc.php 2018-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubAssetHelper
 *
 * Handles connector assets and resolves correct directory paths based on the current shop version.
 *
 * This class simplifies the creation of the HubConnector packages by allowing the app to user dynamic paths.
 */
class HubAssetHelper
{
	/**
	 * @var string
	 */
	protected $versionName;
	
	/**
	 * @var array
	 */
	protected $versionInfo;
	
	
	/**
	 * HubAssetHelper constructor.
	 *
	 * @param string $versionName Installed Gambio shop version.
	 *
	 * @throws InvalidArgumentException If the $version argument is not a string.
	 */
	public function __construct($versionName)
	{
		if(!is_string($versionName)
		   || !$this->isVersionIdentifier($versionName))
		{
			throw new InvalidArgumentException('Invalid Gambio shop version identifier provided: ' . $versionName);
		}
		
		$this->versionName = $versionName;
		$this->versionInfo = $this->extractVersionNumbersFromVersionName($versionName);
	}
	
	
	/**
	 * Get templates base path for the HubConnector according to the version installed.
	 *
	 * @return string the base path for current version's HubConnector templates
	 */
	public function getTemplatesBasePath()
	{
		return $this->resolveTemplatesBasePath();
	}
	
	
	/**
	 * Get script web-assets' base URL for the HubConnector according to the version installed.
	 *
	 * @return string the base URL for current version's HubConnector scripts web-assets
	 */
	public function getScriptsBaseUrl()
	{
		return $this->resolveScriptsBaseUrl();
	}
	
	
	/**
	 * Get style web-assets' base URL for the HubConnector according to the version installed.
	 *
	 * @return string the base URL for current version's HubConnector style web-assets
	 */
	public function getStylesBaseUrl()
	{
		return $this->resolveStylesBaseUrl();
	}
	
	
	/**
	 * Get web-assets' base URL for the HubConnector according to the version installed.
	 *
	 * @return string the base URL for current version's HubConnector general web-assets
	 */
	public function getAssetsBaseUrl()
	{
		return $this->resolveAssetsBaseUrl();
	}
	
	
	/**
	 * Resolves the web asset base URL according to the currently installed shop version
	 *
	 * @return string the base URL for web assets
	 */
	protected function resolveAssetsBaseUrl()
	{
		if($this->isLegacyVersion())
		{
			return 'admin/html/assets';
		}
		else
		{
			return 'GXModules/Gambio/Hub/Build/Admin';
		}
	}
	
	
	/**
	 * Resolves the templates base path according to the installed shop version
	 *
	 * @return string the base path for template assets
	 */
	protected function resolveTemplatesBasePath()
	{
		if($this->isLegacyVersion())
		{
			return 'admin/html/content/hub';
		}
		else
		{
			return 'GXModules/Gambio/Hub/Admin/Html';
		}
	}
	
	
	/**
	 * Resolves the scripts web asset base URL according to the currently installed shop version.
	 *
	 * @return string the base URL for script web assets
	 */
	protected function resolveScriptsBaseUrl()
	{
		if($this->isLegacyVersion())
		{
			return $this->resolveAssetsBaseUrl() . '/javascript/modules/gambio_hub';
		}
		else
		{
			return $this->resolveAssetsBaseUrl() . '/Javascript';
		}
	}
	
	
	/**
	 * Resolves the styles web asset base URL according to the currently installed shop version.
	 *
	 * @return string the base URL for style web assets
	 */
	protected function resolveStylesBaseUrl()
	{
		if($this->isLegacyVersion())
		{
			return $this->resolveAssetsBaseUrl() . '/styles/modules/gambio_hub';
		}
		else
		{
			return $this->resolveAssetsBaseUrl() . '/Styles';
		}
	}
	
	
	/**
	 * Helper function whether the current shop version should resolve.
	 *
	 * @return bool true, if shop version is below 3.5
	 */
	protected function isLegacyVersion()
	{
		return $this->versionInfo[0] < 3 || ($this->versionInfo[0] === 3 && $this->versionInfo[1] < 5);
	}
	
	
	/**
	 * Helper function to verify whether a string is a version identifier.
	 *
	 * @param $versionName string
	 *
	 * @return bool
	 */
	protected function isVersionIdentifier($versionName)
	{
		// omit leading v character for tagged release versions
		$versionName = ltrim($versionName, 'v');
		
		$versionName = preg_replace('/\([^\)]+\)/', '', $versionName);
		$versionName = trim($versionName);
		
		// omit content after and including the first underscore
		$versionPrefix = explode('_', $versionName)[0];
		
		// split x.x.x by decimal dot
		$versionNumbers = explode('.', $versionPrefix);
		
		// the amount of elements
		$unfilteredCount = count($versionNumbers);
		
		// an array omitting empty elements (in case the format was x..x.x or x.x..x etc.)
		$filteredArray = array_filter($versionNumbers, function ($value) {
			return filter_var($value, FILTER_VALIDATE_INT) !== false;
		});
		$filteredCount = count($filteredArray);
		
		return $unfilteredCount === $filteredCount;
	}
	
	
	/**
	 * Helper function to extract the version's key (3.x.x[_...]) from a full versionName
	 *
	 * @param $versionName string the version name
	 *
	 * @return array holding the version numbers
	 */
	protected function extractVersionNumbersFromVersionName($versionName)
	{
		// omit leading v character for tagged release versions
		$versionName = ltrim($versionName, 'v');
		
		// the prefix which should consist of only x.x.x, followed by an underscore with the version's title
		$versionPrefix = explode('_', $versionName)[0];
		
		// extract the scalars identifying the version
		$versionNumbers = explode('.', $versionPrefix);
		
		return array_map('intval', $versionNumbers);
	}
}
