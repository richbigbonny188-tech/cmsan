<?php
/* --------------------------------------------------------------
   AutoUpdaterBackup.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterBackup
 */
class AutoUpdaterBackup
{
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $receiptFile;
	
	/**
	 * @var string
	 */
	protected $directoryPath;
	
	/**
	 * @var array
	 */
	protected $updateName;
	
	/**
	 * @var string
	 */
	protected $creationDate;
	
	/**
	 * @var array
	 */
	protected $newUpdateFiles;
	
	
	/**
	 * AutoUpdaterUpdate constructor.
	 *
	 * @param string $id
	 * @param string $receiptFile
	 * @param string $directoryPath
	 * @param array  $updateName
	 * @param string $creationDate
	 * @param array  $newUpdateFiles
	 */
	public function __construct($id,
	                            $receiptFile,
	                            $directoryPath,
	                            array $updateName,
	                            $creationDate,
	                            array $newUpdateFiles)
	{
		$this->id             = $id;
		$this->receiptFile    = $receiptFile;
		$this->directoryPath  = $directoryPath;
		$this->updateName     = $updateName;
		$this->creationDate   = $creationDate;
		$this->newUpdateFiles = $newUpdateFiles;
	}
	
	
	/**
	 * Creates and returns a new instance of this class.
	 *
	 * @param array $cacheData
	 *
	 * @return \AutoUpdaterBackup
	 */
	public static function createFromDataCache($id, array $cacheData)
	{
		return new self($id, $cacheData['receiptFile'], $cacheData['directoryPath'], $cacheData['updateName'],
		                $cacheData['creationDate'], $cacheData['newUpdateFiles']);
	}
	
	
	/**
	 * Returns the id of this backup.
	 *
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}
	
	
	/**
	 * Returns the receipt filename of the related update.
	 *
	 * @return string
	 */
	public function receiptFile()
	{
		return $this->receiptFile;
	}
	
	
	/**
	 * Returns the path of the directory with the backup files.
	 *
	 * @return array|string
	 */
	public function directoryPath()
	{
		return $this->directoryPath;
	}
	
	
	/**
	 * Returns the update name of the related update.
	 *
	 * @param null|string $language
	 *
	 * @return array|string
	 */
	public function updateName($language = null)
	{
		if($language !== null)
		{
			if(isset($this->updateName[$language]))
			{
				return $this->updateName[$language];
			}
			
			return $this->updateName['en'];
		}
		
		return $this->updateName;
	}
	
	
	/**
	 * Returns the creation date of this backup.
	 *
	 * @return string
	 */
	public function creationDate()
	{
		return $this->creationDate;
	}
	
	
	/**
	 * Returns a list of new files, that came with the related update.
	 *
	 * @return array
	 */
	public function newUpdateFiles()
	{
		return $this->newUpdateFiles;
	}
}