<?php
/* --------------------------------------------------------------
   ScssCompiler.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;

class ScssCompiler implements ScssCompilerInterface
{
    protected Compiler $compiler;
    
    protected string   $basePath            = '';
    
    protected array    $additionalScssFiles = [];
    
    
    /**
     * ScssCompiler constructor.
     *
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setAdditionalScssFiles(array $scss): void
    {
        $this->additionalScssFiles = $scss;
    }
    
    
    /**
     * @inheritDoc
     * @throws SassException
     */
    public function serve($fileToServe): void
    {
        $scssPath          = "{$this->basePath}$fileToServe";
        $scssContentBackup = file_get_contents($scssPath);
        
        $modulesDir = "{$this->basePath}modules/";
        
        $tmpFilenameHash = md5(microtime()) . '_additional0';
        $tmpFiles        = [];
        foreach ($this->additionalScssFiles as $additionalScssFile) {
            $tmpFilenameHash++;
            $tmpFiles[] = $tmpPath = "{$modulesDir}_$tmpFilenameHash.scss";
            
            copy($additionalScssFile, $tmpPath);
            
            // import tmp files
            $importStatement = '@import "modules/' . $tmpFilenameHash . '.scss";';
            file_put_contents($scssPath, "\n$importStatement", FILE_APPEND);
        }
        
        $scssContent = file_get_contents($scssPath);
        try {
            $result = $this->compiler->compileString($scssContent);
            echo $result->getCss();
        } finally {
            //  removing every temporary file
            foreach ($tmpFiles as $tmpFile) {
                @unlink($tmpFile);
            }
            
            //  restoring previous state of the main.scss
            file_put_contents($scssPath, $scssContentBackup);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addImportPath($path): void
    {
        $this->compiler->addImportPath($path);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setVariables(array $variables): void
    {
        $this->compiler->replaceVariables($variables);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setFormatter($formatter): void
    {
        switch ($formatter) {
            case 2:
                $formatter = OutputStyle::EXPANDED;
                break;
            case 4:
            default:
                $formatter = OutputStyle::COMPRESSED;
                break;
        }
        
        $this->compiler->setOutputStyle($formatter);
    }
    
    
    /**
     * @inheritDoc
     */
    public function setBasePath($basePath): void
    {
        $this->basePath = $basePath;
    }
}
