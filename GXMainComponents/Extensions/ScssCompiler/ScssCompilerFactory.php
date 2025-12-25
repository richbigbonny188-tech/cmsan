<?php
/*--------------------------------------------------------------------------------------------------
    ScssCompilerFactory.php 2022-04-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use ScssPhp\ScssPhp\Compiler;

/**
 * Class ScssCompilerFactory
 */
class ScssCompilerFactory
{
    private function __construct()
    {
    }
    
    
    /**
     * @return \ScssCompilerFactory
     */
    public static function create()
    {
        return new self();
    }
    
    
    /**
     * @return ScssCompilerInterface
     */
    public function createCompiler(): ScssCompilerInterface
    {
        if (file_exists(DIR_FS_CATALOG . '.dev-environment') && extension_loaded('sass')) {
            return new SassCompiler();
        } else {
            return new ScssCompiler(new Compiler());
        }
    }
}

/**
 * @param $className
 *
 * @throws \Exception
 */

spl_autoload_register(static function ($className) {
    $base = __DIR__;
    
    $file = $base . DIRECTORY_SEPARATOR . $className . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});