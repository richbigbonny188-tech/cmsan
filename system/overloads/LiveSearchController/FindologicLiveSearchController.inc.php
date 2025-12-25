<?php
/* --------------------------------------------------------------
   FindologicLiveSearchController.inc.php 2018-01-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FindologicLiveSearchController extends FindologicLiveSearchController_parent
{
	public function actionDefault()
	{
		if((string)gm_get_conf('FL_SMARTSUGGEST_SNIPPET') !== '')
		{
			return MainFactory::create('HttpControllerResponse', '');
		}
		else
		{
			return parent::actionDefault();
		}
	}
}
