<?php
/* --------------------------------------------------------------
   MainAutoloader.inc.php 2021-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class MainAutoloader
{
	public function load($p_class)
	{
        MainFactory::loadClass($p_class);
	}
}
