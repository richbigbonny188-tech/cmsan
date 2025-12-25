<?php
/* --------------------------------------------------------------
  MainFactory.inc.php 2024-04-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2024 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class MainFactory
{
    private static $classRegistry = [];
    private static $dataCache;

    private static $cachedDirectories = [];
    private static $originClasses     = [
        'AccountCheck',
        'GMAltText',
        'GMAttributesCalculator',
        'GMC',
        'GMCat',
        'GMCatUpload',
        'GMGPrintCartManager',
        'GMGPrintConfiguration',
        'GMGPrintContentManager',
        'GMGPrintElements',
        'GMGPrintFileManager',
        'GMGPrintOrderElements',
        'GMGPrintOrderManager',
        'GMGPrintOrderSurfaces',
        'GMGPrintOrderSurfacesManager',
        'GMGPrintProductManager',
        'GMGPrintSurfaces',
        'GMGPrintSurfacesGroupsManager',
        'GMGPrintSurfacesManager',
        'GMGPrintWishlistManager',
        'GMGroupIdChecker',
        'GMIPayment',
        'GMIdStarts',
        'GMIloxx',
        'GMJSON',
        'GMJanolaw',
        'GMLightboxControl',
        'GMLogoManager',
        'GMMeta',
        'GMModuleManager',
        'GMOrderFormat',
        'GMPayOne',
        'GMProduct',
        'GMProductUpload',
        'GMSEOBoost',
        'GMSitemap',
        'GMSitemapXML',
        'GMStart',
        'GMStat',
        'GMTSService',
        'GMTabTokenizer',
        'GMUpload',
        'InputFilter',
        'Messages',
        'PclZip',
        'ProductsVariantsCombinator',
        'SMTP',
        'ShowLogs',
        'XMLParser',
        'amazonadvpay',
        'ap',
        'b2czones',
        'breadcrumb',
        'campaigns',
        'cash',
        'categories',
        'cc_validation',
        'chp',
        'chronopost',
        'cod',
        'currencies',
        'dhl',
        'dp',
        'dpd',
        'eustandardtransfer',
        'fedexeu',
        'flat',
        'freeamount',
        'gambio_hub',
        'gambioultra',
        'gmOrderPDF',
        'gmPDF',
        'hpbs',
        'hpcc',
        'hpdc',
        'hpdd',
        'hpddpg',
        'hpgp',
        'hpidl',
        'hpiv',
        'hpivpg',
        'hpmk',
        'hppay',
        'hppf',
        'hppp',
        'hpsu',
        'httpClient',
        'image_manipulation',
        'interkurier',
        'invoice',
        'ipayment',
        'item',
        'language',
        'main',
        'messageStack',
        'moneyorder',
        'objectInfo',
        'order',
        'order_total',
        'ot_cod_fee',
        'ot_coupon',
        'ot_discount',
        'ot_gambioultra',
        'ot_gm_tax_free',
        'ot_gv',
        'ot_loworderfee',
        'ot_payment',
        'ot_ps_fee',
        'ot_shipping',
        'ot_sofort',
        'ot_subtotal',
        'ot_subtotal_no_tax',
        'ot_tax',
        'ot_total',
        'ot_total_netto',
        'ot_tsexcellence',
        'payment',
        'paymentModuleInfo',
        'payone_cc',
        'payone_cod',
        'payone_elv',
        'payone_installment',
        'payone_invoice',
        'payone_master',
        'payone_otrans',
        'payone_prepay',
        'payone_safeinv',
        'payone_wlt',
        'paypal3',
        'postfinance',
        'postfinanceag_amex',
        'postfinanceag_basic',
        'postfinanceag_diners',
        'postfinanceag_mastercard',
        'postfinanceag_twint',
        'postfinanceag_visa',
        'product',
        'sales_report',
        'selfpickup',
        'sepa',
        'shipping',
        'shoppingCart',
        'sofort_sofortueberweisung',
        'splitPageResults',
        'table',
        'upload',
        'ups',
        'upse',
        'wishList',
        'xtcExport',
        'xtcImport',
        'xtcPrice',
        'zones',
        'zonese',
    ];


    /**
     * @deprecated use MainFactory::loadClass instead
     * 
     * @param string $p_class_name
     * @param string|bool $p_class_path
     *
     * @return bool
     */
    public static function load_origin_class(string $p_class_name, bool $p_class_path = false): bool
    {
        self::loadClass($p_class_name);
        
        return true;
    }


    /**
     * @deprecated use MainFactory::loadClass instead
     * 
     * @param string     $p_class_name
     * @param array|null $cache
     * @param bool       $p_consider_theme
     *
     * @return bool
     */
    public static function load_class(string $p_class_name, array &$cache = null, bool $p_consider_theme = true): bool
    {
        self::loadClass($p_class_name);
        
        return true;
    }


    /**
     * Loads the class and any associated overloads and returns the name of the class to use for instantiation.
     * If the class cannot be found, null is returned.
     * 
     * @param string $classWithNamespace
     * @param bool   $rebuildCache
     *
     * @return string|null
     */
    public static function loadClass(string $classWithNamespace, bool $rebuildCache = false): ?string
    {
        $loadClassMappingCacheKey = 'MainFactory-loadClassMapping-' . APPLICATION_RUN_MODE;
        $loadClassMapping         = self::getCache($loadClassMappingCacheKey);

        if (isset($loadClassMapping[$classWithNamespace])
            && class_exists($loadClassMapping[$classWithNamespace],
                            false)) {
            return $loadClassMapping[$classWithNamespace];
        }

        $evalCacheKey = 'MainFactory-eval-' . APPLICATION_RUN_MODE;
        $evalCache    = self::getCache($evalCacheKey);

        if (!isset($evalCache[$classWithNamespace]['code'])) {
            $classFilePath = self::get_class_registry($rebuildCache)->get($classWithNamespace);

            // class not found
            if ($classFilePath === null) {
                return null;
            }

            include_once $classFilePath;

            $evalCache = self::getCache($evalCacheKey);
            if (!isset($evalCache[$classWithNamespace]['code'])) {
                $namespace = self::extractNamespaceFromClassName($classWithNamespace);
                $className = self::getClassNameWithoutNamespace($classWithNamespace);

                $code = "namespace %s { include_once '%s'; }\n";
                $code = sprintf($code, $namespace, $classFilePath);

                $overloadFilePaths = self::getOverloadFilePaths($className, $namespace);

                $parentClassName = $className;

                $isOriginClass = false;
                if (in_array($classWithNamespace, self::$originClasses, true)
                    || (!class_exists($classWithNamespace, false)
                        && class_exists($classWithNamespace . '_ORIGIN', false))) {
                    $isOriginClass         = true;
                    $parentClassName       .= '_ORIGIN';
                    self::$originClasses[] = $classWithNamespace;
                }

                foreach ($overloadFilePaths as $overloadFilePath) {
                    $overloadClassName                    = strtok(basename($overloadFilePath), '.');
                    $overloadParentClassName              = $overloadClassName . '_parent';
                    $overloadParentClassNameWithNamespace = self::buildClassNameWithNamespace($overloadParentClassName,
                                                                                              $namespace);

                    $code .= "namespace %s { 
                                    if (!class_exists('%s', false)) {
                                        class %s extends %s {} 
                                        include_once '%s';
                                    }
                                }\n";
                    $code = sprintf($code,
                                    $namespace,
                                    $overloadParentClassNameWithNamespace,
                                    $overloadParentClassName,
                                    $parentClassName,
                                    $overloadFilePath);

                    $parentClassName = $overloadClassName;
                }

                if ($isOriginClass) {
                    $code .= "namespace %s { 
                                if (!class_exists('%s', false)) {
                                    class %s extends %s {} 
                                }
                            }\n";
                    $code = sprintf($code, $namespace, $classWithNamespace, $className, $parentClassName);
                }

                $evalCache[$classWithNamespace] = ['code' => $code, 'isOriginClass' => $isOriginClass];
                self::writeCache($evalCacheKey, $evalCache);
            }
        } elseif (!isset($loadClassMapping[$classWithNamespace])) {
            // if loadClassMapping cache does not exist but the eval cache (caused by race condition)
            // get last overload class name from eval code
            preg_match('/.*class_exists\(\'([^\']+)\'/s', $evalCache[$classWithNamespace]['code'], $match);
            if (isset($match[1])) {
                $overloadClassName = str_replace('_parent', '', $match[1]);
            }
        }

        eval($evalCache[$classWithNamespace]['code']);

        if (!isset($loadClassMapping[$classWithNamespace])) {
            $namespace = $namespace ?? self::extractNamespaceFromClassName($classWithNamespace);
            $className = $className ?? self::getClassNameWithoutNamespace($classWithNamespace);

            $loadClassMapping[$classWithNamespace] = $namespace !== '' ? $namespace . '\\' : '';
            $loadClassMapping[$classWithNamespace] .= ($evalCache[$classWithNamespace]['isOriginClass']
                                                       || !isset($overloadClassName) ? $className : $overloadClassName);

            self::writeCache($loadClassMappingCacheKey, $loadClassMapping);
        }

        return $loadClassMapping[$classWithNamespace];
    }


    /**
     * MainFactory::create('Class', $argument1, $argument2, ...)
     * short form of
     * MainFactory::create_object('Class', [$argument1, $argument2, ...])
     * 
     * @param string $className
     *
     * @return mixed
     */
    public static function create(string $className)
    {
        $args = func_get_args();
        
        // remove first argument ($className)
        array_shift($args);
        
        return MainFactory::create_object($className, $args);
    }


    /**
     * Returns an instance of the given class.
     *
     * @param string     $className
     * @param array|null $args
     * @param bool       $asSingleton
     * @param bool       $considerTheme
     * @param bool       $rebuildCache
     *
     * @return mixed
     * @throws InvalidArgumentException if class cannot be found
     *
     */
    public static function create_object(
        string $className,
        ?array $args = [],
        bool $asSingleton = false,
        bool $considerTheme = true,
        bool $rebuildCache = false
    ) {
        if ($rebuildCache) {
            self::getDataCache()->clear_cache('directory_cache*');
            self::getDataCache()->clear_cache('ClassRegistry*');
            self::getDataCache()->clear_cache('GXModules*');
            self::getDataCache()->clear_cache('MainFactory*');
        }

        $mappedClassName = self::mapClassName($considerTheme, $className);
        $objectClassName = self::loadClass($mappedClassName, $rebuildCache);

        // if mapped class was not found, try again with original class
        if ($objectClassName === null && $mappedClassName !== $className) {
            $objectClassName = self::loadClass($className, $rebuildCache);
        }

        if ($objectClassName === null) {
            if (!$rebuildCache) {
                // The cache may be no longer valid, so rebuild the cache and try again.
                return self::create_object($className, $args, $asSingleton, $considerTheme, true);
            }

            throw new InvalidArgumentException('Class not found in registry: ' . $className);
        }

        $args = is_array($args) ? $args : [];

        try {
            return $asSingleton ? $objectClassName::get_instance(...$args) : new $objectClassName(...
                $args);
        } catch (Error $error) {
            // Rebuild the cache if a class file does not exist anymore (for example after the deletion of an overload)
            return self::create_object($className, $args, $asSingleton, $considerTheme, true);
        }
    }


    /**
     * @param bool $rebuildCache
     *
     * @return ClassRegistry
     */
    public static function get_class_registry(bool $rebuildCache = false): ClassRegistry
    {
        $currentTemplate = self::_getCurrentTheme();
        
        $coo_class_registry = null;
        if (array_key_exists($currentTemplate, self::$classRegistry) && !$rebuildCache) {
            $coo_class_registry = self::$classRegistry[$currentTemplate];
        }
        
        if ($coo_class_registry === null) {
            # try building object from cache
            $t_cache_key = 'ClassRegistry_' . $currentTemplate;
            $coo_cache   = DataCache::get_instance();
            if ($coo_cache->key_exists($t_cache_key, true)) {
                #use cached object
                $coo_class_registry = $coo_cache->get_data($t_cache_key);
            } else {
                $shopPath = realpath(DIR_FS_CATALOG);
                $shopPath = str_replace('\\', '/', $shopPath) . '/';
                
                # build new registry object
                # directories to be scanned by ClassRegistry
                $t_scan_dirs_array = [
                    $shopPath . 'admin/includes/classes',
                    $shopPath . 'admin/includes/gm/classes',
                    $shopPath . 'gm/classes',
                    $shopPath . 'gm/properties',
                    $shopPath . 'includes/classes',
                    $shopPath . 'includes/modules/order_total',
                    $shopPath . 'includes/modules/payment',
                    $shopPath . 'includes/modules/shipping',
                    $shopPath . 'system/controls',
                    $shopPath . 'system/data',
                    $shopPath . 'system/views',
                    $shopPath . 'system/request_port',
                    $shopPath . 'system/overloads',
                    $shopPath . 'system/extender',
                    $shopPath . 'system/classes',
                    $shopPath . 'system/core',
                    $shopPath . 'GXEngine',
                    $shopPath . 'GXMainComponents',
                    $shopPath . 'GXModules'
                ];
                
                if (file_exists($shopPath . 'PdfCreator/tcpdf.php')) {
                    $t_scan_dirs_array[] = $shopPath . 'PdfCreator';
                }
                
                foreach ($t_scan_dirs_array as $t_key => $t_dir) {
                    if (is_dir($t_dir) === false) {
                        unset($t_scan_dirs_array[$t_key]);
                    }
                }
                
                $coo_class_registry = ClassRegistry::get_instance();
                
                foreach ($t_scan_dirs_array as $t_dir_item) {
                    $coo_class_registry->scan_dir($t_dir_item, true);
                }
                
                #write object to cache
                $coo_cache->set_data($t_cache_key, $coo_class_registry, true);
            }
            self::$classRegistry[$currentTemplate] = $coo_class_registry;
        }
        
        return $coo_class_registry;
    }


    /**
     * @return string
     */
    protected static function _getCurrentTheme(): string
    {
        static $previewThemeHash, $currentThemeHash;
        
        if (self::isPreviewMode()) {
            return $previewThemeHash = $previewThemeHash ?? md5(PREVIEW_THEME);
        }
        
        if (defined('CURRENT_THEME') && !empty(CURRENT_THEME)) {
            return $currentThemeHash = $currentThemeHash ?? md5(CURRENT_THEME);
        }
        
        return 'template_blank';
    }
    
    
    /**
     * @return bool
     */
    protected static function isPreviewMode(): bool
    {
        if (!defined('PREVIEW_MODE')) {
            if (isset($_COOKIE['STYLE_EDIT_PREVIEW_THEME'])) {
                $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR
                        . $_COOKIE['STYLE_EDIT_PREVIEW_THEME'] . DIRECTORY_SEPARATOR . 'preview.json';
                
                if (file_exists($file)) {
                    define('PREVIEW_MODE', true);
                    define('PREVIEW_THEME', $_COOKIE['STYLE_EDIT_PREVIEW_THEME']);
                } else {
                    setcookie("STYLE_EDIT_PREVIEW_THEME", "", time() - 1);
                    define('PREVIEW_MODE', false);
                }
            } else {
                define('PREVIEW_MODE', false);
            }
        }
        
        return PREVIEW_MODE;
    }
    
    
    /**
     * Maps a ContentView class name to a ThemeContentView, if the theme systems is considered.
     * 
     * @param bool   $considerTheme
     * @param string $className
     *
     * @return string
     */
    private static function mapClassName(bool $considerTheme, string $className): string
    {
        # try to create ThemeContentViews instead of normal ContentViews
        if ($considerTheme && strpos($className, 'ContentView') !== false
            && strpos($className, 'ThemeContentView') === false) {
            $className = str_replace('ContentView', 'ThemeContentView', $className);
        }
        
        return $className;
    }


    /**
     * Returns an array of overload file paths for given class and namespace.
     * 
     * @param string $className
     * @param string $namespace
     *
     * @return array
     */
    private static function getOverloadFilePaths(string $className, string $namespace = ''): array
    {
        if (self::isUnitTestRunning()) {
            return [];
        }

        $cacheKey = 'MainFactory-overloadFilePaths-' . APPLICATION_RUN_MODE;

        $overloadFilePathsCache = self::getCache($cacheKey);

        $classNameWithNamespace = self::buildClassNameWithNamespace($className, $namespace);

        if (isset($overloadFilePathsCache[$classNameWithNamespace])) {
            return $overloadFilePathsCache[$classNameWithNamespace];
        }

        $systemOverloadsFilePaths = self::scanDirectory(self::getSystemOverloadsDirectoryPath($className));
        $moduleOverloadsFilePaths = self::getModuleOverloadFilePaths($className);

        $overloadFilePaths = array_merge($systemOverloadsFilePaths, $moduleOverloadsFilePaths);

        self::removeFilesWithWrongNamespace($overloadFilePaths, $namespace);
        $overloadFilePathsCache[$classNameWithNamespace] = $overloadFilePaths;
        self::writeCache($cacheKey, $overloadFilePathsCache);

        return $overloadFilePathsCache[$classNameWithNamespace];
    }


    /**
     * Scans given directory recursively for *.php files and returns them as an array sorted alphabetically.
     * 
     * @param string $path
     *
     * @return array
     */
    private static function scanDirectory(string $path): array
    {
        $filePaths = array();

        if(!empty(self::$cachedDirectories[$path]))
        {
            $cachedDirectory = self::$cachedDirectories[$path];
            $cachedDirectory->reset_count_index();
        }
        else
        {
            $cachedDirectory         = new CachedDirectory($path);
            self::$cachedDirectories[$path] = $cachedDirectory;
        }

        if($cachedDirectory->is_dir($path) == false)
        {
            # return empty array, if overload directory not found
            return $filePaths;
        }

        $fileSuffix = '.php';

        while(($entry = $cachedDirectory->read()) !== false)
        {
            if($entry[0] === '.')
            {
                continue;
            }

            # entry is a file and ends with '.php'
            if($cachedDirectory->is_file($path . '/' . $entry)
               && strpos($entry, $fileSuffix, strlen($entry) - strlen($fileSuffix)) > 0
            )
            {
                $t_system_class_path = $path . '/' . $entry;
                $filePaths[] = $t_system_class_path;
            }
        }

        sort($filePaths);

        return $filePaths;
    }


    /**
     * @return bool
     */
    private static function isUnitTestRunning(): bool
    {
        return defined('UNIT_TEST_RUNNING') && constant('UNIT_TEST_RUNNING');
    }


    /**
     * Returns the according overload folder for the class in the system/overloads directory.
     * 
     * @param string $className
     *
     * @return string
     */
    private static function getSystemOverloadsDirectoryPath(string $className): string
    {
        return DIR_FS_CATALOG . 'system/overloads/' . self::getOverloadFolderName($className);
    }
    
    
    /**
     * Returns the overload folder name considering classes existing twice in the shop.
     *
     * @param string $className
     *
     * @return string
     */
    private static function getOverloadFolderName(string $className): string
    {
        $classesExistingTwice = [
            'language',
            'messageStack',
            'order',
            'shoppingCart',
            'splitPageResults',
        ];
        
        return in_array($className, $classesExistingTwice, true)
               && APPLICATION_RUN_MODE === 'backend' ? 'Admin-' . $className : $className;
    }


    /**
     * Removes files with wrong namespace from given file paths array.
     * 
     * @param array  $filePaths
     * @param string $namespace
     */
    private static function removeFilesWithWrongNamespace(array &$filePaths, string $namespace): void
    {
        foreach ($filePaths as $filePath) {
            $handle = @fopen($filePath, 'r');
            if ($handle) {
                while (($line = fgets($handle, 4096)) !== false) {
                    preg_match('/^\s*namespace\s+([^;]+)\s*;/', $line, $matches);

                    if (isset($matches[1]) && $matches[1] !== $namespace) {
                        fclose($handle);
                        unset($filePaths[$filePath]);
                        continue 2;
                    }
                }
                fclose($handle);
            }
        }
    }


    /**
     * @param string $classWithNamespace
     *
     * @return string
     */
    private static function extractNamespaceFromClassName(string $classWithNamespace): string
    {
        $length = strrpos($classWithNamespace, '\\');
        return $length !== false ? substr($classWithNamespace,
                                                                         0,
                                                                         $length) : '';
    }


    /**
     * @param string $classWithNamespace
     *
     * @return string
     */
    private static function getClassNameWithoutNamespace(string $classWithNamespace): string
    {
        $namespaceEndPosition = strrpos($classWithNamespace, '\\');
        return $namespaceEndPosition !== false ? substr($classWithNamespace, $namespaceEndPosition + 1) : $classWithNamespace;
    }


    /**
     * @param string $className
     * @param string $namespace
     *
     * @return string
     */
    private static function buildClassNameWithNamespace(string $className, string $namespace): string
    {
        $finalClassWithNamespace = $namespace !== '' ? $namespace . '\\' : '';
        $finalClassWithNamespace .= $className;
        
        return $finalClassWithNamespace;
    }


    /**
     * @return array
     */
    private static function getOverloadDirectories(): array
    {
        static $overloadDirectories;
        
        if ($overloadDirectories === null) {
            $overloadDirectories = [];
            $files               = GXModulesCache::getInstalledModuleFiles();

            foreach ($files as $file) {
                $strpos = stripos($file, 'overloads');

                if ($strpos) {
                    $directoryPath                       = substr($file, 0, $strpos + strlen('overloads/'));
                    $overloadDirectories[$directoryPath] = $directoryPath;
                }
            }
        }

        return $overloadDirectories;
    }


    /**
     * @param string $className
     *
     * @return array
     */
    private static function getModuleOverloadFilePaths(string $className): array
    {
        static $moduleOverloadsFilePaths;
        
        $overloadDirectories = self::getOverloadDirectories();
        $folderName          = self::getOverloadFolderName($className);
        
        if (!isset($moduleOverloadsFilePaths[$folderName])) {
            $moduleOverloadsFilePaths[$folderName] = [];

            foreach ($overloadDirectories as $directoryPath) {
                $moduleOverloadsFilePaths[$folderName] = array_merge($moduleOverloadsFilePaths[$folderName],
                                                                    self::scanDirectory($directoryPath . $folderName));
            }
        }

        sort($moduleOverloadsFilePaths[$folderName]);

        return $moduleOverloadsFilePaths[$folderName];
    }


    /**
     * @return DataCache
     */
    private static function getDataCache(): DataCache
    {
        if (self::$dataCache === null) {
            self::$dataCache = DataCache::get_instance();
        }
        
        return self::$dataCache;
    }


    /**
     * @param string $cacheKey
     *
     * @return mixed
     */
    private static function getCache(string $cacheKey)
    {
        try {
            $cache = self::getDataCache()->get_data($cacheKey, true);
        } catch (\UnexpectedValueException $e) {
            $cache = [];
        }
        
        return $cache;
}


    /**
     * @param string $cacheKey
     * @param array  $data
     */
    private static function writeCache(string $cacheKey, array $data): void
    {
        self::getDataCache()->add_data($cacheKey, $data, true);
    }
}
