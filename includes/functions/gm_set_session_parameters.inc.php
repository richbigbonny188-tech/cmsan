<?php
/* --------------------------------------------------------------
   gm_set_session_parameters.inc.php 2020-07-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Session\SessionNamePostfixGenerator;
use Gambio\Core\Session\SessionRepository;
use Gambio\Core\Session\SessionService;

function gm_set_session_parameters()
{
    $host       = HTTP_SERVER;
    $webPath    = rtrim(DIR_WS_CATALOG, '/');
    $serverPath = rtrim(DIR_FS_CATALOG, '/');
    
    $url        = new Url($host, $webPath);
    $path       = new Path($serverPath);
    $repository = new SessionRepository(new SessionNamePostfixGenerator(), $path);
    
    $sslEnabled = (defined('ENABLE_SSL') && is_bool(constant('ENABLE_SSL')) && constant('ENABLE_SSL') === true)
                  || (defined('ENABLE_SSL_CATALOG') && is_string(constant('ENABLE_SSL_CATALOG'))
                      && strtolower(constant('ENABLE_SSL_CATALOG')) === 'true');
    
    $server = new Server($sslEnabled, ''); // request url is not needed here
    
    $service = new SessionService($repository, $url, $path, $server);
    $service->setupSession();
}
