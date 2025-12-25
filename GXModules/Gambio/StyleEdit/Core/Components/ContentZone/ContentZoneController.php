<?php
/* --------------------------------------------------------------
  ContentZoneController.php 2019-10-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone;

use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Components\ContentZone\Commands\ContentZoneSaveCommand;
use Gambio\StyleEdit\Core\Components\ContentZone\Entities\ContentZoneOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Services\ContentZoneService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use ReflectionException;
use stdClass;

/**
 * Class ContentZoneController
 */
class ContentZoneController extends BasicController
{
    /**
     * @var ContentZoneSaveCommand
     */
    protected $saveCommand;
    
    /**
     * @var ContentZoneService
     */
    protected $service;
    
    
    /**
     * ContentZoneController constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->saveCommand = SingletonPrototype::instance()->get('ContentZoneSaveCommand');
        $this->service     = SingletonPrototype::instance()->get(ContentZoneService::class);
    }
    
    
    /**
     * @param array $uri
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function get(array $uri)
    {
        if (count($uri) === 3) {
            return $this->outputJson($this->service->getAll()->getArray());
        } elseif (count($uri) === 4) {
            $contentZoneIds = end($uri);
            $data           = [];
            foreach (explode(',', $contentZoneIds) as $id) {
                try {
                    $data[] = $this->service->getById($id);
                } catch (Exception $e) {
                    //ignore error messages and only answer an empty array
                }
            }
            return $this->outputJson($data);
        }
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     * @throws Exception
     */
    public function put(array $uri, $data)
    {
        if (strtolower($uri[3]) === 'save') {
            
            $contentZoneJson   = json_decode($data, false);
            $contentZoneOption = ContentZoneOption::createfromJsonObject($contentZoneJson);
            
            $this->saveCommand->setOption($contentZoneOption);
            
            $this->saveCommand->execute();
            
            $response          = new stdClass;
            $response->success = true;
            
            return $this->outputJson($response);
        }
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