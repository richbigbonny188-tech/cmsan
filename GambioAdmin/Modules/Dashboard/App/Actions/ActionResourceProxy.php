<?php
/*------------------------------------------------------------------------------
 ActionResourceProxy.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\App\Actions;

use Curl\Curl;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;

/**
 * Class ActionResourceProxy
 *
 * @package Gambio\Admin\Modules\Dashboard\App\Actions
 * @codeCoverageIgnore
 */
class ActionResourceProxy extends AbstractAction
{
    /**
     * @var Curl
     */
    private $curl;
    
    
    /**
     * ActionResourceProxy constructor.
     *
     * @param Curl $curl
     */
    public function __construct(
        Curl $curl
    ) {
        $this->curl = $curl;
    }
    
    
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        $javascript = $this->curl->get('https://www.gambio.com/files/admin-news/js/' . $request->getAttribute('filename'));
        
        return $response->withHeader('Content-type', 'text/javascript')->write($javascript);
    }
}