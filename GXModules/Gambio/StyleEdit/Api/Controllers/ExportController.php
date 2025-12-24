<?php
/* --------------------------------------------------------------
  ExportController.php 2019-12-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Api\Controllers;

use Exception;
use Gambio\StyleEdit\Core\Components\BasicController;
use Gambio\StyleEdit\Core\Services\ExportDefaultsService;
use Gambio\StyleEdit\Core\Services\ExportImagesService;
use Gambio\StyleEdit\Core\Services\ExportService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Slim\Psr7\Stream;

/**
 * Class ExportController
 * @package Gambio\StyleEdit\Api\Controllers
 */
class ExportController extends BasicController
{
    
    /**
     * @var string
     */
    protected $themeId;
    
    /**
     * @var ExportService
     */
    protected $exportService;
    
    
    /**
     * Copies a theme and bundles all required files ( e.g. images )
     * into the theme and changes required paths
     *
     * @param array $uri
     *
     * @return mixed
     * @throws Exception
     */
    public function get(array $uri)
    {
        $this->init($uri);
        
        $zipPath = $this->exportService()->exportTheme($this->themeId);
        
        $this->executeFileDownloadResponse($zipPath);
        
        $this->exportService()->deleteExportedZip($zipPath);
    }
    
    
    /**
     * @param array $uri
     *
     * @throws Exception
     */
    protected function init(array $uri) : void
    {
        if (!isset($uri[2])) {
            
            throw new Exception('No theme was specified');
        }
        
        $this->themeId = $uri[2];
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
    
    
    /**
     * @return ExportService
     * @throws Exception
     */
    protected function exportService() : ExportService
    {
        if ($this->exportService === null) {
            
            $this->exportService = SingletonPrototype::instance()->get(ExportService::class);
        }
        
        return $this->exportService;
    }
    
    
    /**
     * @param string $filepath
     *
     * @throws Exception
     */
    protected function executeFileDownloadResponse(string $filepath) : void
    {
        $this->slim()
             ->getResponseFactory()
             ->createResponse()
             ->withHeader('Content-Type', 'application/zip')
             ->withHeader('Pragma', 'public')
             ->withHeader('Content-Disposition', 'attachment; filename=' . basename($filepath))
             ->withHeader('Content-Transfer-Encoding', 'binary')
             ->withHeader('Content-Length', filesize($filepath));
        ob_clean();
        flush();
        readfile($filepath);
        exit;
    }
    
    
    /**
     * @return \Slim\App
     *
     * @throws Exception
     */
    protected function slim() : \Slim\App
    {
        return SingletonPrototype::instance()->get(\Slim\App::class);
    }
}