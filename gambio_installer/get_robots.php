<?php
/* --------------------------------------------------------------
   get_robots.php 2018-10-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

define('SUPPRESS_REDIRECT', true);

chdir('../admin');

require_once 'includes/application_top.php';
require_once DIR_FS_CATALOG . 'gm/inc/get_robots.php';

if(isset($_GET['download']) && $_GET['download'] === 'robot')
{
	get_robots(DIR_WS_CATALOG);
}
