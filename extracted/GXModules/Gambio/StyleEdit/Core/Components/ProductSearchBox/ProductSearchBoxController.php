<?php
/*--------------------------------------------------------------------------------------------------
    ProductSearchBoxController.php 2020-05-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace GXModules\Gambio\StyleEdit\Core\Components\ProductSearchBox;


use Gambio\StyleEdit\Adapters\Interfaces\ProductSearchAdapterInterface;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\RequestedThemeInterface;

class ProductSearchBoxController  extends BasicController
{
    /**
     * @var ProductSearchAdapterInterface
     */
    private $adapter;

    /**
     * ProductSearchBoxController constructor.
     *
     * @param RequestedThemeInterface|null $requestedTheme
     * @param ProductSearchAdapterInterface $adapter
     */
    public function __construct(ProductSearchAdapterInterface $adapter, RequestedThemeInterface $requestedTheme = null)
    {
        parent::__construct($requestedTheme);
        $this->adapter = $adapter;
    }

    protected function parseParams($paramString) {
        $searchData=[];
        foreach(explode('&',$paramString) as $value){
            $values = explode('=',str_replace('+',' ',$value));
            if(count($values) === 2){
                $searchData[$values[0]] = $values[1];
            }
        };
        return $searchData;
    }

    /**
     * @inheritDoc
     */
    public function get(array $uri)
    {
        $searchData=$this->parseParams($uri[3]);

        $search = explode('=', explode('&',$uri[3])[1]);
        if(isset($searchData['term']))
        {
            return json_encode($this->adapter->searchByTerm($searchData['term']));
        }
        elseif(isset($searchData['ids'])) {
            return json_encode($this->adapter->searchByIds(explode(',',$searchData['ids'])));
        }

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