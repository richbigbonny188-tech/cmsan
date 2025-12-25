<?php
/* --------------------------------------------------------------
   ScssCompilerInterface.php 2019-04-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ScssCompilerInterface
 */
interface ScssCompilerInterface
{
    const STYLE_NESTED     = 1;
    const STYLE_EXPANDED   = 2;
    const STYLE_COMPACT    = 3;
    const STYLE_COMPRESSED = 4;
    
    
    /**
     * @param string[]|string $scss Path to scss files
     */
    public function setAdditionalScssFiles(array $scss);
    
    
    /**
     * @param string $fileToServe
     */
    public function serve($fileToServe);
    
    
    /**
     * @param $path
     */
    public function addImportPath($path);
    
    
    /**
     * @param array $variables
     */
    public function setVariables(array $variables);
    
    
    /**
     * @param int|mixed $formatter
     */
    public function setFormatter($formatter);
    
    
    /**
     * @param string $basePath
     */
    public function setBasePath($basePath);
    
}