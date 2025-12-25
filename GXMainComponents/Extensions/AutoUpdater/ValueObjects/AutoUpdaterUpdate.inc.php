<?php
/* --------------------------------------------------------------
   AutoUpdaterUpdate.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterUpdate
 */
class AutoUpdaterUpdate
{
	/**
	 * @var string
	 */
	protected $receiptFile;
	
	/**
	 * @var bool
	 */
	protected $required;
	
	/**
	 * @var array
	 */
	protected $name;
	
	/**
	 * @var array
	 */
	protected $description;
	
	/**
	 * @var array
	 */
	protected $icon;
	
	/**
	 * @var array
	 */
	protected $image;
	
	/**
	 * @var string
	 */
	protected $versionHistoryName;
	
	/**
	 * @var string
	 */
	protected $infoboxIdentifier;
	
	/**
	 * @var string
	 */
	protected $zip;
	
	/**
	 * @var string
	 */
	protected $zipHash;
	
	/**
	 * @var array
	 */
	protected $fileList;
	
	
	/**
	 * AutoUpdaterUpdate constructor.
	 *
	 * @param string $receiptFile
	 * @param bool   $required
	 * @param array  $name
	 * @param array  $description
	 * @param array  $icon
	 * @param array  $image
	 * @param string $versionHistoryName
	 * @param string $infoboxIdentifier
	 * @param string $zip
	 * @param string $zipHash
	 * @param array  $fileList
	 */
	public function __construct($receiptFile,
	                            $required,
	                            array $name,
	                            array $description,
	                            array $icon,
	                            array $image,
	                            $versionHistoryName,
	                            $infoboxIdentifier,
	                            $zip,
	                            $zipHash,
	                            array $fileList)
	{
		$this->receiptFile        = $receiptFile;
		$this->required           = $required;
		$this->name               = $name;
		$this->description        = $description;
		$this->icon               = $icon;
		$this->image              = $image;
		$this->versionHistoryName = $versionHistoryName;
		$this->infoboxIdentifier  = $infoboxIdentifier;
		$this->zip                = $zip;
		$this->zipHash            = $zipHash;
		$this->fileList           = $fileList;
	}
	
	
	/**
	 * Creates and returns an auto updater update object.
	 *
	 * @param array $update
	 *
	 * @return \AutoUpdaterUpdate
	 */
	public static function createByUpdateServerResponse(array $update)
	{
		return new self($update['receipt'], $update['required'], $update['name'], $update['description'],
		                $update['icon'], $update['image'], $update['versionHistoryName'], $update['infoboxIdentifier'],
		                $update['zip'], $update['zip_hash'], $update['filelist']);
    }
    
    
    /**
     * Creates and returns an auto updater update object.
     *
     * @param array $gambioStoreData
     *
     * @return \AutoUpdaterUpdate
     */
    public static function createByGambioStoreData(array $gambioStoreData)
    {
        $langCode = isset($_SESSION['language_code']) ? $_SESSION['language_code'] : 'de';
        $hash     = md5($gambioStoreData['details']['id']);
        
        return new self($hash, true, $gambioStoreData['details']['title'], $gambioStoreData['details']['description'],
            [], [], $hash, $hash, $gambioStoreData['fileList']['zip']['source'],
            $gambioStoreData['fileList']['zip']['hash'], $gambioStoreData['fileList']['includedFiles']);
    }
	
	
	/**
	 * Returns the ID of the update.
	 *
	 * @return string
	 */
	public function id()
	{
		return md5($this->receiptFile);
	}
	
	
	/**
	 * Returns the receipt filename of the update.
	 *
	 * @return string
	 */
	public function receiptFile()
	{
		return $this->receiptFile;
	}
	
	
	/**
	 * Returns the status of the required flag of the update.
	 *
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required;
	}
	
	
	/**
	 * Returns the name of the update.
	 *
	 * @param null|string $language
	 *
	 * @return array|string If a language is given, the name for the given language will be returned.
	 */
	public function name($language = null)
	{
		if($language !== null)
		{
			if(!isset($this->name[$language]))
			{
				$language = 'en';
			}
			
			return $this->name[$language];
		}
		
		return $this->name;
	}
	
	
	/**
	 * Returns the description of the update.
	 *
	 * @param null|string $type
	 * @param null|string $language
	 *
	 * @return array|string If a type is given, the descriptions for the given type will be returned. If also a
	 *                      language is given, the descriptions for the given type and language will be returned.
	 */
	public function description($type = null, $language = null)
	{
		if($type !== null)
		{
			if(!isset($this->description[$type]))
			{
				$type = 'short';
			}
			
			if($language !== null)
			{
				if(!isset($this->description[$type][$language]))
				{
					$language = 'en';
				}
				
				return $this->description[$type][$language];
			}
			
			return $this->description[$type];
		}
		
		return $this->description;
	}
	
	
	/**
	 * Returns the icon class of the update.
	 *
	 * @param null|string $language
	 *
	 * @return array|string If a language is given, the icon for the given language will be returned.
	 */
	public function icon($language = null)
	{
		if($language !== null)
		{
			if(!isset($this->icon[$language]))
			{
				$language = 'en';
			}
			
			return $this->icon[$language];
		}
		
		return $this->icon;
	}
	
	
	/**
	 * Returns the image url of the update.
	 *
	 * @param null|string $language
	 *
	 * @return array|string If a language is given, the image for the given language will be returned.
	 */
	public function image($language = null)
	{
		if($language !== null)
		{
			if(!isset($this->image[$language]))
			{
				$language = 'en';
			}
			
			return $this->image[$language];
		}
		
		return $this->image;
	}
	
	
	/**
	 * Returns the version history name of the update,
	 *
	 * @return string
	 */
	public function versionHistoryName()
	{
		return $this->versionHistoryName;
	}
	
	
	/**
	 * Returns the identifier of the admin infobox message,
	 *
	 * @return string
	 */
	public function infoboxIdentifier()
	{
		return $this->infoboxIdentifier;
	}
	
	
	/**
	 * Returns the zip file url of the update.
	 *
	 * @return string
	 */
	public function zip()
	{
		return $this->zip;
	}
	
	
	/**
	 * Returns the zip file md5 hash of the update.
	 *
	 * @return string
	 */
	public function zipHash()
	{
		return $this->zipHash;
	}
	
	
	/**
	 * Returns a list of update files of the update.
	 *
	 * @return array
	 */
	public function fileList()
	{
		return $this->fileList;
	}
}