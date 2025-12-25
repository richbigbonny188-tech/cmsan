<?php
/* --------------------------------------------------------------
   KlarnaHubCountries.inc.php 2017-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class KlarnaHubCountries extends KlarnaHubCountries_parent
{
	public function get_all_countries()
	{
		return StaticGXCoreLoader::getDatabaseQueryBuilder()->get('countries')->result_array();
	}
}