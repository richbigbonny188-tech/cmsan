<?php

/* --------------------------------------------------------------
   GambioHubEnvironmentClassFinderSettings.inc.php 2017-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubEnvironmentClassFinderSettings extends GambioHubEnvironmentClassFinderSettings_parent
{
	/**
	 * Returns an numeric array with all directories that will be accepted by the ClassFinder.
	 *
	 * @return array
	 */
	public function getAllowedDirectories()
	{
		$allowedDirsArray = parent::getAllowedDirectories(); 
		$allowedDirsArray[] = DIR_FS_CATALOG . 'system/classes/gambio_hub/controllers';
		
		return $allowedDirsArray;
	}
}