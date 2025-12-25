<?php

/* --------------------------------------------------------------
   ModuleTitleProvider.inc.php 2018-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ModuleTitleProvider
 *
 * @category System
 * @package  Shared
 */
abstract class ModuleTitleProvider
{
    /**
     * @var \LanguageTextManager
     */
    protected static $language;
    
    /**
     * @var string
     */
    protected static $type;
    
    
    /**
     * Returns the module title value.
     * The value gets manipulated by the strip_tags and trim functions.
     *
     * @param string $methodName        Name of module method.
     * @param string $languageDirectory Path to language directory.
     *
     * @return string Stripped module title.
     */
    public static function getStrippedTagsTitle($methodName, $languageDirectory = '')
    {
        $title = trim(strip_tags(static::getTitle($methodName, $languageDirectory)));
        
        return $title === '' ? $methodName : $title;
    }
    
    
    public function title($methodName, $languageDirectory = '')
    {
        return static::getTitle($methodName, $languageDirectory);
    }
    
    
    /**
     * Returns the title of a module module.
     *
     * @param string $methodName        Name of module method.
     * @param string $languageDirectory Path to language directory.
     *
     * @return string Module title.
     */
    public static function getTitle($methodName, $languageDirectory = '')
    {
        static::_validateMethodNameArgument($methodName);
        static::_validateLanguageDirectoryArgument($languageDirectory);
        static::_getLanguageTextManager()->init_from_lang_file('lang/' . $languageDirectory . '/modules/'
                                                               . static::$type . '/' . $methodName . '.php');
        
        $titleConstant = 'MODULE_' . strtoupper(static::$type) . '_' . strtoupper($methodName) . '_TEXT_TITLE';
        
        if (defined($titleConstant)) {
            return constant($titleConstant);
        }
        // dynamic includes are only testable with a lot of pain, so we just ignore the following part
        // @codeCoverageIgnoreStart
        elseif (file_exists(DIR_FS_CATALOG . 'includes/modules/' . static::$type . '/' . basename($methodName)
                            . '.php')) {
            include_once DIR_FS_CATALOG . 'includes/modules/' . static::$type . '/' . basename($methodName) . '.php';
            
            if (defined($titleConstant)) {
                return constant($titleConstant);
            }
            
            if (class_exists($methodName)) {
                $module = MainFactory::create($methodName);
                
                if (isset($module->title) && trim($module->title) !== '') {
                    return trim($module->title);
                }
            }
        }
        
        // @codeCoverageIgnoreEnd
        
        return $methodName;
    }
    
    
    /**
     * Validates the method name argument.
     * The referenced value gets manipulated by the basename and trim functions.
     *
     * @param string $methodName Name of module method.
     */
    protected static function _validateMethodNameArgument(&$methodName)
    {
        if (!is_string($methodName)) {
            throw new InvalidArgumentException('$methodName (' . gettype($methodName) . ') is not a string');
        }
        $methodName = trim($methodName);
    }
    
    
    /**
     * Validates the language directory argument.
     * The referenced value gets manipulated by the basename and trim functions.
     *
     * @param string $languageDirectory Path of language directory.
     */
    protected static function _validateLanguageDirectoryArgument(&$languageDirectory)
    {
        if (!is_string($languageDirectory)) {
            throw new InvalidArgumentException('$languageDirectory (' . gettype($languageDirectory)
                                               . ') is not a string');
        }
        $languageDirectory = basename(trim($languageDirectory));
        if ($languageDirectory === '') {
            $languageDirectory = $_SESSION['language'];
        }
    }
    
    
    /**
     * Returns the language text manager instance.
     *
     * @return LanguageTextManager
     */
    protected static function _getLanguageTextManager()
    {
        if (null === static::$language) {
            static::$language = MainFactory::create_object('LanguageTextManager', [], true);
        }
        
        return static::$language;
    }
}