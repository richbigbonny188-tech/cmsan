<?php
/* --------------------------------------------------------------
   AutoUpdaterFilesystem.inc.php 2018-09-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\Filesystem;
use League\Flysystem\Util;

/**
 * Class AutoUpdaterFilesystem
 */
class AutoUpdaterFilesystem extends Filesystem
{
	/**
	 * @var \League\Flysystem\AdapterInterface|\AutoUpdaterFtpAdapter|\AutoUpdaterSFtpAdapter
	 */
	protected $adapter;
	
	
	/**
	 * Change file permissions.
	 *
	 * @param string $path
	 * @param int $mode
	 *
	 * @return bool
	 */
	public function chmod($path, $mode)
	{
		$path = Util::normalizePath($path);
		
		return @$this->adapter->chmod($path, $mode);
	}
}