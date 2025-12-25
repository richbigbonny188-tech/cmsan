<?php
/* --------------------------------------------------------------
   GxScssServer.php 2019-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Leafo\ScssPhp;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Version;

class GxScssServer extends Server
{
    /**
     * @var string
     */
    protected $basePath;
    
    /**
     * @var array
     */
    protected $additionalScssFiles = [];
    
    
    /**
     * GxScssServer constructor.
     *
     * @param string        $dir
     * @param string|null   $cacheDir
     * @param Compiler|null $scss
     */
    public function __construct($dir, $cacheDir = null, $scss = null)
    {
        $this->basePath = $dir;
        
        parent::__construct($dir, $cacheDir, $scss);
    }
    
    
    /**
     * @param string[] $scss
     */
    public function setAdditionalScssFiles(array $scss)
    {
        $this->additionalScssFiles = $scss;
    }
    
    
    /**
     * @param string $salt
     */
    public function serve($salt = '')
    {
        $fileName = md5(microtime()) . '_additional0';
        
        $mainScssPath = $this->findInput();
        
        $mainScssContent = file_get_contents($mainScssPath);
        
        $moduleDirectory = $this->basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
        
        $createdFiles = [];
        
        //  Every additional scss file needs to be copied temporary to the module directory
        while (count($this->additionalScssFiles)) {
            $scssPath       = array_shift($this->additionalScssFiles);
            $createdFiles[] = $tempPath = $moduleDirectory . '_' . ++$fileName . '.scss';
            
            copy($scssPath, $tempPath);
            
            //  the temporary file must be imported in the main.scss
            $importStatement = '@import "modules/' . $fileName . '.scss";';
            file_put_contents($mainScssPath, "\n$importStatement", FILE_APPEND);
        }
        
        //  This will start the compilation and echos the result
        parent::serve($salt);
        
        //  removing every temporary file
        while (count($createdFiles)) {
            unlink(array_shift($createdFiles));
        }
        
        //  restoring previous state of the main.scss
        file_put_contents($mainScssPath, $mainScssContent);
    }
}