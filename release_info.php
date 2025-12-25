<?php
/* --------------------------------------------------------------
   release_info.php 2021-04-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once __DIR__ . '/vendor/psr/container/src/ContainerInterface.php';
include_once __DIR__ . '/GambioCore/Application/DependencyInjection/Registry.php';
include_once __DIR__ . '/GambioCore/Application/Application.php';

return $gx_version = Gambio\Core\Application\Application::VERSION;
