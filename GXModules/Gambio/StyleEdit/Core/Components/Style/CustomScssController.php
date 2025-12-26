<?php
/*--------------------------------------------------------------------------------------------------
    CustomStyleController.php 2019-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Style;

use Exception;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class CustomScssController
 * @package Gambio\StyleEdit\Core\Components\Style
 */
class CustomScssController extends BasicController
{
    
    
    /**
     * @var CustomScssService
     */
    private $customScssService;
    
    
    /**
     * CustomScssController constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->customScssService = SingletonPrototype::instance()->get(CustomScssService::class);
    }
    
    
    /**
     * @param array $uri
     *
     * @return mixed
     * @throws Exception
     */
    public function get(array $uri)
    {
        return $this->outputJson($this->customScssService->getCustomStyles());
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function put(array $uri, $data)
    {
        // TODO: Implement put() method.
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function post(array $uri, $data)
    {
        // TODO: Implement post() method.
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function delete(array $uri, $data)
    {
        // TODO: Implement delete() method.
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function patch(array $uri, $data)
    {
        // TODO: Implement patch() method.
    }
    
    
    /**
     * @return BasicController
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }
}