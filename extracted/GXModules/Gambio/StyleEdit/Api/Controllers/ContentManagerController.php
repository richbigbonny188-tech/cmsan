<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerController.php 2021-02-16
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Api\Controllers;

use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\RequestedThemeInterface;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Adapters\Interfaces\ContentManagerAdapterInterface;

class ContentManagerController extends BasicController
{
    /**
     * @var ContentManagerAdapterInterface
     */
    private $adapter;
    
    
    public function __construct(ContentManagerAdapterInterface $adapter, RequestedThemeInterface $requestedTheme = null)
    {
        parent::__construct($requestedTheme);
        $this->adapter = $adapter;
    }
    
    public function get(array $uri)
    {
        $data = $this->adapter->getAllContentPages();
        
        return $this->outputJson(['success' => true, 'data' => $data]);
    }
    
    
    public function put(array $uri, $data)
    {
        // TODO: Implement put() method.
    }
    
    
    public function post(array $uri, $data)
    {
        // TODO: Implement post() method.
    }
    
    
    public function delete(array $uri, $data)
    {
        // TODO: Implement delete() method.
    }
    
    
    public function patch(array $uri, $data)
    {
        // TODO: Implement patch() method.
    }
    
    
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }
}
