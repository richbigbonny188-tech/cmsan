<?php

/* --------------------------------------------------------------
   HubAutoloader.inc.php 2017-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HubAutoloader
 * 
 * Use this class for shop versions in order to enable Hub autoloading
 */
class HubAutoloader
{
	/**
	 * Registers autoloading if needed. 
	 */
	public static function register()
	{
		if(class_exists('\HubPublic\ValueObjects\HubClientKey'))
		{
			return;
		}
		
		spl_autoload_register(function ($class)
		{
			// project-specific namespace prefix
			$prefix = 'HubPublic\\';
			
			// base directory for the namespace prefix
			$base_dir = DIR_FS_CATALOG . 'vendor/gambio-hub/hubpublic/src/';
			
			// does the class use the namespace prefix?
			$len = strlen($prefix);
			if(strncmp($prefix, $class, $len) !== 0)
			{
				// no, move to the next registered autoloader
				return;
			}
			
			// get the relative class name
			$relative_class = substr($class, $len);
			
			// replace the namespace prefix with the base directory, replace namespace
			// separators with directory separators in the relative class name, append
			// with .php
			$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
			
			// if the file exists, require it
			if(file_exists($file))
			{
				require $file;
			}
		});
	}
}