<?php
/* --------------------------------------------------------------
   ScssCompiler.php 2019-03-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once __DIR__ . '/../../../includes/classes/scssphp-0.5.1/scss.inc.php';
include_once 'GxScssServer.php';

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compressed;
use Leafo\ScssPhp\Formatter\Nested;
use Leafo\ScssPhp\Formatter\Expanded;
use Leafo\ScssPhp\Formatter\Compact;
use Leafo\ScssPhp\GxScssServer;

/**
 * Class ScssCompiler
 */
class ScssCompilerBackup implements ScssCompilerInterface
{
    /**
     * @var array
     */
    protected $additionalScssFiles = [];
    
    /**
     * @var string
     */
    protected $basePath;
    
    /**
     * @var \Leafo\ScssPhp\Compiler
     */
    protected $compiler;
    
    /**
     * @var \Leafo\ScssPhp\GxScssServer
     */
    protected $server;
    
    
    /**
     * ScssCompiler constructor.
     */
    public function __construct()
    {
        $this->compiler = new Compiler;
    }
    
    
    /**
     * @param string[]|string $scss Path to scss files
     */
    public function setAdditionalScssFiles(array $scss)
    {
        $this->additionalScssFiles = $scss;
    }
    
    
    /**
     * @param string $fileToServe
     *
     * @throws \Exception
     */
    public function serve($fileToServe)
    {
        if (!isset($this->basePath)) {
            throw new \Exception('BasePath not defined');
        }
        
        if ($fileToServe === '') {
            throw new \Exception('fileToServe can\'t be empty');
        }
        
        //  \Leafo\ScssPhp\Server expects the file to serve to be transmitted via get
        $_GET['p'] = $fileToServe;
        
        $this->server = new GxScssServer($this->basePath, 'cache', $this->compiler);
        $this->server->setAdditionalScssFiles($this->additionalScssFiles);
        $this->server->serve();
    }
    
    
    /**
     * @param $path
     */
    public function addImportPath($path)
    {
        $this->compiler->addImportPath($path);
    }
    
    
    /**
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->compiler->setVariables($variables);
    }
    
    
    /**
     * @param int|mixed $formatter ScssCompilerInterface::STYLE_CONSTANT (1-4)
     */
    public function setFormatter($formatter)
    {
        
        switch ($formatter) {
            
            //  STYLE_NESTED
            case 1:
                $formatter = Nested::class;
                break;
            
            //  STYLE_EXPANDED
            case 2:
                $formatter = Expanded::class;
                break;
            
            //  STYLE_COMPACT
            case 3:
                $formatter = Compact::class;
                break;
            
            //  STYLE_COMPRESSED
            case 4:
            default:
                
                $formatter = Compressed::class;
                break;
        }
        
        $this->compiler->setFormatter($formatter);
    }
    
    
    /**
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
}