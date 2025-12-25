<?php
/* --------------------------------------------------------------
   CustomThemeJavaScriptController.inc.php 2019-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CustomThemeJavaScriptController
{
    /*
     * @var string[]
     */
    protected $javaScriptMap = [];

    /*
     *
     */
    protected $update = false;

    /*
     *
     */
    protected static $publicThemeCustomJsPath = SHOP_ROOT . 'public/theme/javascripts/system';

    /*
     *
     */
    const JAVASCRIPT_SCRIPT_NAME_PATTERN = '/^(.*)(\.min)(\.js)/i';

    /*
     *
     */
    const JAVASCRIPT_IS_MINIFIED = '/\.min/i';


    /**
     * CustomThemeJavaScriptController constructor.
     * @param string|null $publicThemeCustomJsPath
     */
    public function __construct(?string $publicThemeCustomJsPath = null)
    {
        if ($publicThemeCustomJsPath) {
            self::$publicThemeCustomJsPath = $publicThemeCustomJsPath;
        }

        $this->mapCustomJavaScripts();
    }


    /**
     * @param string $publicThemeCustomJsPath
     */
    public static function setPublicThemeCustomJsPath($publicThemeCustomJsPath)
    {
        self::$publicThemeCustomJsPath = $publicThemeCustomJsPath;
    }


    /**
     * @param string $page
     * @param string $script
     *
     * @throws \Exception
     */
    public function includeScript($page, $script)
    {
        if (!isset($this->javaScriptMap[$page][$script])) {
            throw new Exception("No script found with the name '$script' in Directory '$page'");
        }

        include $this->javaScriptMap[$page][$script];
    }


    /**
     * @param string|null $page
     * @param string $script
     *
     * @return bool|string
     * @throws \Exception
     */
    public function getJavaScript($page, $script)
    {
        if (!isset($this->javaScriptMap[$page][$script])) {
            throw new Exception("No script found with the name '$script' in Directory '$page'");
        }

        return file_get_contents($this->javaScriptMap[$page][$script]);
    }

    /**
     *  Locating every custom javascript
     *
     *  .min files are higher prioritized than source files
     */
    protected function mapCustomJavaScripts()
    {
        $systemFriendlyPath = str_replace('/', DIRECTORY_SEPARATOR,
                self::$publicThemeCustomJsPath) . DIRECTORY_SEPARATOR;

        if ($this->javaScriptMap !== [] || !is_dir($systemFriendlyPath)) {
            return;
        }

        //  Locating every custom javascript file
        foreach (new DirectoryIterator($systemFriendlyPath) as $dir) {
            if (!$dir->isDot() && $dir->isDir()) {
                $directoryName = $dir->getFilename();
                $customDirectory = $dir->getPath() . DIRECTORY_SEPARATOR . $directoryName;

                foreach (new DirectoryIterator($customDirectory) as $script) {
                    if (!$script->isDot() && $script->isFile()) {
                        $customJavascriptFile = $script->getPath() . DIRECTORY_SEPARATOR . $script->getFilename();
                        $arrayIndex = preg_replace(self::JAVASCRIPT_SCRIPT_NAME_PATTERN, '$1$3',
                            $script->getFilename());

                        if (!isset($this->javaScriptMap[$directoryName][$arrayIndex])) {
                            $this->javaScriptMap[$directoryName][$arrayIndex] = [];
                        }

                        $this->javaScriptMap[$directoryName][$arrayIndex][] = $customJavascriptFile;
                    }
                }
            }
        }


        //  prioritizing .min files over source files
        foreach ($this->javaScriptMap as &$customJavascriptDirectoryPath) {
            foreach ($customJavascriptDirectoryPath as &$customJavascriptFile) {
                if (count($customJavascriptFile) > 1) {
                    $customJavascriptFile = array_filter($customJavascriptFile, static function ($file) {
                        return preg_match(self::JAVASCRIPT_IS_MINIFIED, $file) === 1;
                    });
                }

                $customJavascriptFile = array_pop($customJavascriptFile);
            }
        }
    }


    /**
     * @param null|string $page
     *
     * @return string[] list of custom javascript's that must be appended to the theme
     */
    public function getJavaScripts($page = null)
    {
        $scripts = [];

        if (isset($this->javaScriptMap['Global'])) {
            foreach ($this->javaScriptMap['Global'] as $scriptPath) {
                $scripts[] = preg_replace(self::JAVASCRIPT_SCRIPT_NAME_PATTERN, '$1$3', $scriptPath);
            }
        }

        if ($page !== null && isset($this->javaScriptMap[$page])) {
            foreach ($this->javaScriptMap[$page] as $scriptPath) {
                $scripts[] = preg_replace(self::JAVASCRIPT_SCRIPT_NAME_PATTERN, '$1$3', $scriptPath);
            }
        }

        return $scripts;
    }
}
