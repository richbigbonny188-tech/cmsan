<?php
/* --------------------------------------------------------------
   .env.sample.php 2023-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * The env system is still in the experimental phase. Changes up to the complete omission in a future version are
 * possible. Only use it if you know exactly what you are doing.
 */

return [
    'APP_SECURITY_TOKEN'                     => '',
    'APP_SENTRY_DSN'                         => '',
    'MYSQL_SQL_MODE'                         => '',    # only configures Doctrine Connection
    'MYSQL_OVERWRITE_SQL_MODE'               => true,  # only configures Doctrine Connection
    'LOG_WARNINGS'                           => true,  # if true, errors with warning level will be logged
    'LOG_SUPPRESSED_WARNINGS'                => false, # if false, no warnings will be logged, if the php code line contains the @-operator
    'LOG_DEPRECATED_WARNINGS'                => false, # if true, E_DEPRECATED and E_USER_DEPRECATED errors will be logged
    'SMARTY_MUTE_UNDEFINED_OR_NULL_WARNINGS' => true,  # if true, Smarty does not throw an error, if a variable does not exist
];
