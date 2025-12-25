<?php
/*--------------------------------------------------------------------------------------------------
    CategorySearchBoxController.php 2020-05-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace GXModules\Gambio\StyleEdit\Core\Components\CategorySearchBox;


use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\RequestedThemeInterface;
use GXModules\Gambio\StyleEdit\Adapters\Interfaces\CategorySearchAdapterInterface;

class CategorySearchBoxController   extends BasicController
{
    /**
     * @var CategorySearchAdapterInterface
     */
    protected $adapter;

    /**
     * ProductSearchBoxController constructor.
     *
     * @param CategorySearchAdapterInterface $adapter
     * @param RequestedThemeInterface|null $requestedTheme
     */
    public function __construct(CategorySearchAdapterInterface $adapter, RequestedThemeInterface $requestedTheme = null)
    {
        parent::__construct($requestedTheme);
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function get(array $uri)
    {
        $term = explode('=', explode('&',$uri[3])[1]);
        return json_encode($this->adapter->searchByTerm($term[1]));
    }

    /**
     * @inheritDoc
     */
    public function put(array $uri, $data)
    {
        // TODO: Implement put() method.
    }

    /**
     * @inheritDoc
     */
    public function post(array $uri, $data)
    {
        // TODO: Implement post() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(array $uri, $data)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function patch(array $uri, $data)
    {
        // TODO: Implement patch() method.
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }
}