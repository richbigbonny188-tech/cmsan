<?php
/* --------------------------------------------------------------
   ThemeControl.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * This class make use of the php-sass module wich uses the libsass library.
 * Class SassCompiler
 */
class SassCompiler implements ScssCompilerInterface
{
    /**
     * @var \Sass
     */
    protected $instance;
    protected $additionalScssFiles = [];
    protected $importPaths         = [];
    protected $additionalVariables = [];
    protected $basePath            = '';
    
    
    public function __construct()
    {
        $this->instance = new Sass();
    }
    
    
    /**
     * @param string[]|string $scss Path to scss files
     */
    public function setAdditionalScssFiles(array $scss)
    {
        foreach ($scss as $item) {
            $this->additionalScssFiles[] = $item;
        }
    }
    
    
    /**
     * @param string $fileToServe
     *
     * @return mixed
     */
    public function serve($fileToServe)
    {
        $this->instance->setIncludePath($this->parseIncludePaths());
        
        $content = $this->parseVariables();
        
        if (!file_exists($fileToServe) && file_exists($this->basePath . $fileToServe)) {
            $fileToServe = realpath($this->basePath . $fileToServe);
        }
        
        $content .= $this->parseFile($fileToServe);
        $content .= $this->parseAdditionalScssFiles();
        try {
            
            $result = $this->instance->compile($content);
        } catch (SassException $exception) {
            throw $exception;
        }
        
        echo $result;
    }
    
    
    /**
     * @return string
     */
    protected function parseIncludePaths()
    {
        
        $paths   = $this->importPaths;
        $baseDir = realpath(__DIR__ . '/../../../') . DIRECTORY_SEPARATOR;
        foreach ($paths as $key => $path) {
            if (is_dir($baseDir . $path)) {
                $path = $baseDir . $path;
            }
            $paths[$key] = $path;
        }
        
        return implode(':', array_unique($paths));
    }
    
    
    /**
     * @return string
     */
    protected function parseVariables()
    {
        $result = '';
        foreach ($this->additionalVariables as $name => $value) {
            $result .= "\$$name:              $value;\n";
        }
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    protected function parseAdditionalScssFiles()
    {
        $content = '';
        foreach ($this->additionalScssFiles as $file) {
            $content .= $this->parseFile($file);
        }
        
        return $content;
    }
    
    
    /**
     * @param $filename
     *
     * @return bool|string
     */
    protected function parseFile($filename)
    {
        
        if (file_exists($filename)) {
            return file_get_contents($filename) . "\n";
        }
        
        return false;
    }
    
    
    /**
     * @param $path
     *
     * @return mixed
     */
    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }
    
    
    /**
     * @return array
     */
    public function getImportPath()
    {
        return $this->importPaths;
    }
    
    
    /**
     * @param array $variables
     *
     * @return mixed
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $name => $value) {
            $this->additionalVariables[$name] = $value;
        }
    }
    
    
    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->additionalVariables;
    }
    
    
    /**
     * @param $formatter
     *
     * @return mixed
     */
    public function setFormatter($formatter)
    {
        switch ($formatter) {
            case ScssCompilerInterface::STYLE_NESTED:
                $this->instance->setStyle(Sass::STYLE_NESTED);
                break;
            case ScssCompilerInterface::STYLE_EXPANDED:
                $this->instance->setStyle(Sass::STYLE_EXPANDED);
                break;
            case ScssCompilerInterface::STYLE_COMPACT:
                $this->instance->setStyle(Sass::STYLE_COMPACT);
                break;
            case ScssCompilerInterface::STYLE_COMPRESSED:
                $this->instance->setStyle(Sass::STYLE_COMPRESSED);
                break;
        }
    }
    
    
    /**
     * @return int
     */
    public function getFormatter()
    {
        switch ($this->instance->getStyle()) {
            case Sass::STYLE_NESTED:
                return ScssCompilerInterface::STYLE_NESTED;
            
            case Sass::STYLE_EXPANDED :
                return ScssCompilerInterface::STYLE_EXPANDED;
            
            case Sass::STYLE_COMPACT:
                return ScssCompilerInterface::STYLE_COMPACT;
            
            case Sass::STYLE_COMPRESSED:
                return ScssCompilerInterface::STYLE_COMPRESSED;
        }
    }
    
    
    /**
     * @param $basePath
     *
     * @return mixed
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
}