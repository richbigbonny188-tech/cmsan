<?php
/* --------------------------------------------------------------
   configure.php 2017-05-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
header("HTTP/1.1 302 Found");
header('Cache-Control: no-cache');
header('Location: gambio_installer/');
die();
?>