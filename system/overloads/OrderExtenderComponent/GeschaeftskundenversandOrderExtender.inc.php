<?php
/* --------------------------------------------------------------
	GeschaeftskundenversandOrderExtender.inc.php 2017-06-15
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class GeschaeftskundenversandOrderExtender extends GeschaeftskundenversandOrderExtender_parent
{
	/**
	 * Generic proceed method
	 */
	public function proceed()
	{
		parent::proceed();
		if(gm_get_conf('MODULE_CENTER_GESCHAEFTSKUNDENVERSAND_INSTALLED') == true) {
			$scriptFileUrl = DIR_WS_ADMIN.'html/assets/javascript/modules/geschaeftskundenversand/geschaeftskundenversand-orderdetails.min.js';
			$scriptTag     = sprintf('<div class="gkv_orderdetails"><script src="%s"></script></div>', $scriptFileUrl);
			$this->addContentToCollection('below_history', $scriptTag, '');
		}
	}

}
