<?php
/*--------------------------------------------------------------
   HtmlProvider.php 2020-08-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Html;

use Curl\Curl;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\EndpointUrl;
use Throwable;

/**
 * Class HtmlProvider
 * @package Gambio\Admin\Modules\Dashboard\Html
 */
class HtmlProvider
{
    /**
     * @var EndpointUrl
     */
    protected $endpointUrl;
    
    /**
     * @var Curl
     */
    protected $curl;
    
    
    /**
     * HtmlProvider constructor.
     *
     * @param EndpointUrl                      $endpointUrl
     * @param Curl                             $curl
     */
    public function __construct(
        EndpointUrl $endpointUrl,
        Curl $curl
    ) {
        $this->endpointUrl = $endpointUrl;
        $this->curl        = $curl;
    }
    
    
    /**
     * @return string
     * @throws Throwable
     */
    public function dashboardHtml(): string
    {
        return $this->curl->get($this->endpointUrl->value());
    }
}