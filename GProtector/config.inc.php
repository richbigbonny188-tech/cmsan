<?php
/* --------------------------------------------------------------
  config.inc.php 2019-06-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

define('GAMBIO_PROTECTOR_DIR', __DIR__ . '/');
define('GAMBIO_PROTECTOR_CLASSES_DIR', __DIR__ . '/classes/');
define('GAMBIO_PROTECTOR_CONNECTORS_DIR', __DIR__ . '/classes/connectors/');
define('GAMBIO_PROTECTOR_FUNCTIONS_DIR', __DIR__ . '/functions/');
define('GAMBIO_PROTECTOR_FILTER_DIR', __DIR__ . '/filter/');
define('GAMBIO_PROTECTOR_LOG_DIR', dirname(dirname(__FILE__)) . '/logfiles/');
define('GAMBIO_PROTECTOR_TOKEN_DIR', dirname(dirname(__FILE__)) . '/media/');
define('GAMBIO_PROTECTOR_CACHE_DIR', __DIR__ . '/cache/');
define('GAMBIO_PROTECTOR_BASE_DIR', dirname(dirname(__FILE__)) . '/');
define('GAMBIO_PROTECTOR_FUNCTION_PREFIX', 'gprotector_');
define('GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX', 'gprotector_token_');
define('GAMBIO_PROTECTOR_FILE_PATTERN', '*.json');
define('GAMBIO_PROTECTOR_GZIP_LOG', true);
define('GAMBIO_PROTECTOR_LOG_MAX_FILESIZE', 1); // megabytes
define('GAMBIO_PROTECTOR_REMOTE_FILTERRULES_URL', 'https://protect.gambio-server.net/standard.json');
define('GAMBIO_PROTECTOR_LOCAL_FILERULES_DIR', __DIR__ . '/filter/');
define('GAMBIO_PROTECTOR_LOCAL_FILERULES_FILENAME', 'standard.json');
define('GAMBIO_PROTECTOR_CACHE_FILERULES_FILENAME', 'standard.json');
define('GAMBIO_PROTECTOR_CACHE_RENEW_INTERVAL', 8*60*60); // In seconds