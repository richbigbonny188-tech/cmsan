<?php
/* --------------------------------------------------------------
 MenuSettings.php 2020-10-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached;

use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class MenuSettings
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuSettings
{
    /**
     * @var MenuUrl
     */
    private $url;
    
    /**
     * @var Server
     */
    private $server;
    
    
    /**
     * MenuSettings constructor.
     *
     * @param Url    $url
     * @param Server $server
     */
    public function __construct(
        Url $url,
        Server $server
    ) {
        $this->url    = $url;
        $this->server = $server;
    }
    
    
    /**
     * @return string
     */
    public function adminUrl(): string
    {
        return $this->url->admin();
    }
    
    
    /**
     * @return string
     */
    public function baseUrl(): string
    {
        return $this->url->base();
    }
    
    
    /**
     * @return string
     */
    public function requestUri(): string
    {
        return $this->server->requestUri();
    }
}