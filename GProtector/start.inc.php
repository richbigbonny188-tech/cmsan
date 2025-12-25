<?php
/* --------------------------------------------------------------
  start.inc.php 2021-03-17
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use GProtector\FilterCache;
use GProtector\FilterReader;
use GProtector\GProtector;

error_reporting(
    E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_CORE_ERROR & ~E_CORE_WARNING
);

if (!class_exists('\Composer\Autoload\ClassLoader', false)) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/config.inc.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/GProtectorLogConnectorInterface.inc.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/GProtector.inc.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/Filter.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/FilterCache.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/FilterCollection.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/FilterReader.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/Key.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/MetaData.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/Method.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/ScriptName.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/ScriptNameCollection.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/Severity.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/Variable.php';
require_once GAMBIO_PROTECTOR_CLASSES_DIR . '/VariableCollection.php';


$gprotector = new GProtector(new FilterReader(), new FilterCache());
$gprotector->start();
unset($gprotector);
