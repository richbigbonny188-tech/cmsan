<?php
/* --------------------------------------------------------------
   SettingsController.php 2019-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\Settings;

use Exception;
use Gambio\StyleEdit\Api\Storage\StyleEditExpertModeStorage;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\SingletonPrototype;
use IdType;
use stdClass;

/**
 * Class SettingsController
 * @package Gambio\StyleEdit\Core\Components\Settings
 */
class SettingsController extends BasicController
{
    
    /**
     * @param array $uri
     *
     * @return mixed
     */
    public function get(array $uri)
    {
        
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function put(array $uri, $data)
    {
        
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function post(array $uri, $data)
    {
        
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @return mixed
     */
    public function delete(array $uri, $data)
    {
        
    }
    
    
    /**
     * @param array $uri
     * @param       $data
     *
     * @throws Exception
     */
    public function patch(array $uri, $data): void
    {
        $data = json_decode($data, false);
        
        if (isset($data->expertMode)) {
            $this->setExpertMode($data);
        }
    }
    
    
    /**
     * @param stdClass $data
     *
     * @throws Exception
     */
    protected function setExpertMode(stdClass $data): void
    {
        $customerId = new IdType((int)$data->customer_id);
        
        if (is_numeric($data->expertMode)) {
            $data->expertMode = (bool)$data->expertMode;
        } elseif (is_string($data->expertMode)) {
            $data->expertMode = strtolower($data->expertMode) === 'true';
        }
    
        SingletonPrototype::instance()
            ->get(StyleEditExpertModeStorage::class)
            ->setExpertMode($customerId, $data->expertMode);
    }
    
    
    /**
     * @return void
     */
    public function __clone()
    {
        
    }
}