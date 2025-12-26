<?php

/* --------------------------------------------------------------
   VersionInfoFileReader.inc.php 2017-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VersionInfoFileReader
 *
 * @category    System
 * @package     VersionInfo
 * @subpackage  Reader
 * @extends     AbstractVersionInfoReader
 */
class VersionInfoFileReader extends AbstractVersionInfoReader
{
    /**
     * @var string
     */
    protected $versionInfoFilePath;
    
    /**
     * @var VersionInfoItemFactory
     */
    protected $versionInfoItemFactory;
    
    
    /**
     * VersionInfoFileReader constructor.
     *
     * @param ExistingDirectory      $existingDirectory
     * @param VersionInfoItemFactory $versionInfoItemFactory
     */
    public function __construct(ExistingDirectory $existingDirectory, VersionInfoItemFactory $versionInfoItemFactory)
    {
        $this->versionInfoFilePath    = $existingDirectory->getDirPath();
        $this->versionInfoItemFactory = $versionInfoItemFactory;
    }
    
    
    /**
     * Returns all VersionInfoFileItems from file system.
     *
     * @return VersionInfoFileItem[]
     */
    public function getAllVersionInfoItems()
    {
        $versionInfoArray = [];
        
        foreach (new DirectoryIterator($this->versionInfoFilePath) as $directoryItem) {
            if ($directoryItem->isDot() || $directoryItem->isDir()
                || false !== strpos($directoryItem->getFilename(), 'build')
                || $directoryItem->getExtension() !== 'php') {
                continue;
            }
            
            $versionInfoFilename = new NonEmptyStringType($directoryItem->getFilename());
            $versionInfoFilePath = new NonEmptyStringType($directoryItem->getPathname());
            
            $name                = new NonEmptyStringType($this->_determineName($versionInfoFilename));
            $version             = new NonEmptyStringType(strtolower($this->_determineVersion($versionInfoFilePath)));
            $vendor              = new NonEmptyStringType($this->_determineVendor($versionInfoFilename));
            $changelogVersionURL = new NonEmptyStringType($this->_determineChangelogURL($version, $vendor, $name));
            $installationDate    = new DateTime('@' . $directoryItem->getCTime());
            $installedStatus     = new BoolType(true);
            
            $versionInfoArray[] = $this->versionInfoItemFactory->createVersionInfoFileItem($name,
                                                                                           $version,
                                                                                           $vendor,
                                                                                           $changelogVersionURL,
                                                                                           $installationDate,
                                                                                           $installedStatus);
        }
        
        return $versionInfoArray;
    }
    
    
    /**
     * Determines the vendor of the version info items while considering known edge cases.
     *
     * @param NonEmptyStringType $versionInfoFilename
     *
     * @return string $versionInfoItemName
     */
    protected function _determineVendor(NonEmptyStringType $versionInfoFilename)
    {
        $versionInfoFilename = $versionInfoFilename->asString();
        $versionInfoFilename = str_replace('.php', '', $versionInfoFilename);
        $versionInfoFilename = str_replace('_', '.', $versionInfoFilename);
        
        switch ($versionInfoFilename) {
            case strpos($versionInfoFilename, 'update-') !== false:
            case strpos($versionInfoFilename, 'sp-') !== false:
            case strpos($versionInfoFilename, 'mu-') !== false:
            case strpos($versionInfoFilename, 'ku-') !== false:
            case strpos($versionInfoFilename, 'mobile.template') !== false:
            case strpos($versionInfoFilename, 'MobileCandy') !== false:
            case strpos($versionInfoFilename, 'sec.update') !== false:
            case strpos($versionInfoFilename, 'gambio.hub') !== false:
            case preg_match('/[sS]ec(urity)?[-\._ ]([pP]atch|[uU]pdate).*/', $versionInfoFilename) ? true : false:
            case is_numeric(str_replace('.', '', $versionInfoFilename)) !== false:
            case strpos($versionInfoFilename, 'BETA') !== false:
            case strpos($versionInfoFilename, 'beta') !== false:
                $vendor = 'Gambio';
                break;
            default:
                $vendor = ' ';
        }
        
        return $vendor;
    }
    
    
    /**
     * Determines the name of the version info items while considering known edge cases.
     *
     * @param NonEmptyStringType $versionInfoFilename
     *
     * @return string $versionInfoItemName
     */
    protected function _determineName(NonEmptyStringType $versionInfoFilename)
    {
        $versionInfoFilename = $versionInfoFilename->asString();
        $versionInfoFilename = strtolower($versionInfoFilename);
        $versionInfoFilename = str_replace('.php', '', $versionInfoFilename);
        $versionInfoFilename = str_replace('_', '.', $versionInfoFilename);
        
        return parent::_determineName(new NonEmptyStringType($versionInfoFilename));
    }
    
    
    /**
     * Determines the version of the version info items while considering known edge cases.
     *
     * @param NonEmptyStringType $versionInfoFilePath
     *
     * @return string $version
     */
    protected function _determineVersion(NonEmptyStringType $versionInfoFilePath)
    {
        $versionInfoFilePath = $versionInfoFilePath->asString();
        $version             = ' ';
        
        $directoryItemFile = fopen($versionInfoFilePath, 'r');
        while ($line = fgets($directoryItemFile)) {
            if (strpos($line, 'version') !== false) {
                if (strpos($line, 'mobile_template-') !== false) {
                    $line = str_replace('_', '.', $line);
                    $line = str_replace('mobile.template-', '', $line);
                    $line = substr($line, 0, strpos($line, '-gx'));
                }
                
                if (strpos($line, 'Master Update') !== false) {
                    $line = str_replace('Master Update', '', $line);
                }
                
                $version = trim(str_replace('version:', '', $line));
                
                break;
            }
        }
        fclose($directoryItemFile);
        
        return $version;
    }
}