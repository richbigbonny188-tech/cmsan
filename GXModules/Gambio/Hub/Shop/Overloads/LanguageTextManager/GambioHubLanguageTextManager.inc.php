<?php
/* --------------------------------------------------------------
   GambioHubLanguageTextManager.inc.php 2017-02-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LanguageTextManager
 */
class GambioHubLanguageTextManager extends GambioHubLanguageTextManager_parent
{
    protected function _initMappingArray()
    {
	    parent::_initMappingArray();
	
	    if (isset(self::$languages)) {
		    foreach(self::$languages as $language)
		    {
			    self::$sectionMappings['lang/' . $language['directory'] . '/modules/payment/gambio_hub.php'] = 'gambio_hub';
		    }
	    } else {
		    foreach($this->languages as $language)
		    {
			    $this->sectionMappings['lang/' . $language['directory'] . '/modules/payment/gambio_hub.php'] = 'gambio_hub';
		    }
	    }
    }
}