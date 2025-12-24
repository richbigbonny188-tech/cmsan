<?php
/* --------------------------------------------------------------
   ClassRegistry.inc.php 2021-03-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ClassRegistry extends Registry
{
	/**
	 * pattern for "which one is a class file"
	 */
	public $v_file_pattern        = '.php';
	public $v_samples_dir_pattern = '_samples';

    /**
     * @var string[] 
     */
	protected $mapping = [
        'banktransfer_validation' => 'AccountCheck',
        'datamatrix'              => 'Datamatrix',
        'GMCounter'               => 'GMC',
        'GMModulesManager'        => 'GMModuleManager',
        'hermes'                  => 'Hermes',
        'http_client'             => 'httpClient',
        'image_manipulator_GD2'   => 'image_manipulation',
        'JSON'                    => 'Services_JSON',
        'LangFileReader'          => 'OldLanguageFileReader',
        'message_stack'           => 'messageStack',
        'object_info'             => 'objectInfo',
        'payment_module_info'     => 'paymentModuleInfo',
        'pclzip'                  => 'PclZip',
        'pdf417'                  => 'PDF417',
        'shopping_cart'           => 'shoppingCart',
        'split_page_results'      => 'splitPageResults',
        'table_block'             => 'tableBlock',
        'tcpdf'                   => 'TCPDF',
        'tcpdf_colors'            => 'TCPDF_COLORS',
        'tcpdf_filters'           => 'TCPDF_FILTERS',
        'tcpdf_font_data'         => 'TCPDF_FONT_DATA',
        'tcpdf_fonts'             => 'TCPDF_FONTS',
        'tcpdf_images'            => 'TCPDF_IMAGES',
        'tcpdf_static'            => 'TCPDF_STATIC',
        'wish_list'               => 'wishList',
        'xmlparserv4'             => 'XMLParser',
    ];

    /**
     * @var string[] 
     */
	protected $fileNameMapping = [
        'class.heidelpaygw.php' => 'heidelpayGW',
        'class.inputfilter.php' => 'InputFilter',
        'class.skrill.php'      => 'fcnt_skrill',
        'class.smtp.php'        => 'SMTP',
    ];
	
	/**
	 * ClassRegistry constructor.
	 */
	public function __construct()
	{
		if(isset($GLOBALS['coo_debugger']) && is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('ClassRegistry() by ' . gm_get_env_info('REQUEST_URI'), 'ClassRegistry');
		}
	}
	
	
	public static function &get_instance()
	{
		static $s_instance;
		
		if($s_instance === null)
		{
			$s_instance = new ClassRegistry();
		}
		
		return $s_instance;
	}
	
	
	/**
	 * Scans given dir recursively or not for classes ('.php') and sets class names and paths.
	 *
	 * @param string $p_path        path for scan
	 * @param bool   $p_recursively do it with or without
	 *
	 * @return bool true:ok | false:error
	 */
	public function scan_dir($p_path, $p_recursively = false)
	{
		$t_coo_cached_directory = new CachedDirectory($p_path);
		
		if($t_coo_cached_directory->is_dir($p_path) === false)
		{
			# p_path not a directory
			return false;
		}
		elseif(substr($p_path, strlen($this->v_samples_dir_pattern) * -1) === $this->v_samples_dir_pattern)
		{
			# p_path is samples-directory
			return false;
		}
		
		while(false !== ($t_entry = $t_coo_cached_directory->read()))
		{
			if($t_entry[0] === '.')
			{
				continue;
			}
			
			if(substr($t_entry, -13) === '.lang.inc.php')
			{
				continue;
			}
			
			$t_part = '/';
			if(substr($p_path, -1) === $t_part)
			{
				$t_part = '';
			}
			
			$strposOffset = strlen($t_entry) - strlen($this->v_file_pattern);
			if($strposOffset < 0)
			{
				$strposOffset = 0;
			}
			
			if($t_coo_cached_directory->is_dir($p_path . '/' . $t_entry) && $p_recursively)
			{
				$this->scan_dir($p_path . $t_part . $t_entry, $p_recursively);
			}
			elseif(strpos($t_entry, $this->v_file_pattern, $strposOffset) > 0)
			{
                $className = strtok($t_entry, '.');
                $filePath  = realpath($p_path . '/' . $t_entry);
                $this->set($this->getClassNameWithNamespace($className, $filePath), $filePath);
			}
		}
		
		return true;
	}
    
    
    /**
     * Returns class with namespace. The namespace is detected by searching for namespace statement in its file.
     *
     * @param $className
     * @param $filePath
     *
     * @return string
     */
	protected function getClassNameWithNamespace($className , $filePath) {
        $className = $this->mapClassName($className, $filePath);

        $handle = @fopen($filePath, 'r');
        if ($handle) {
            while (($line = fgets($handle, 4096)) !== false) {
                // search for namespace in line
                preg_match('/^\s*namespace\s+([^;]+)\s*;/', $line, $matches);
                
                if (isset($matches[1])) {
                    // add namespace to class name
                    $className = $matches[1] . '\\' . $className;
                    break;
                }
    
                // stop search for namespace if line begins with class, abstract, interface, trait or a variable
                preg_match('/^\s*(class|abstract|interface|trait|\$[\S]+)\s+/', $line, $matches2);
    
                if (isset($matches2[1])) {
                    break;
                }
            }
            fclose($handle);
        }
        
        return $className;
    }


    /**
     * Returns the actual class name.
     * 
     * @param $className
     * @param $filePath
     *
     * @return string
     */
    protected function mapClassName($className, $filePath): string
    {
        $className = $this->mapping[$className] ?? $className;

        // handle edge case classes being in a file having a class.* prefix
        if ($className === 'class') {
            $fileName = basename($filePath);

            $className = $this->fileNameMapping[$fileName] ?? $className;
        }

        return $className;
    }
}
