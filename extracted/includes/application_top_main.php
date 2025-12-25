<?php
/* --------------------------------------------------------------
   application_top_main.php 2019-08-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once __DIR__ . '/../GXMainComponents/ApplicationMain.inc.php';

$application = new Gambio\GX\ApplicationMain();
$application->run();
