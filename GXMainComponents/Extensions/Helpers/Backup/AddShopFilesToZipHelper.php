<?php
/* --------------------------------------------------------------
  AddShopFilesToZipHelper.php 2019-10-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class AddShopFilesToZipHelper
 */
class AddShopFilesToZipHelper
{
    /**
     * @var PclZip
     */
    protected $zip;
    
    /**
     * @var array|string[]
     */
    protected $fileList;
    
    /**
     * @var string
     */
    protected $shopRoot;
    
    /**
     * @var int
     */
    protected $optionFlag;
    
    /**
     * @var string[]
     */
    protected $additionalFiles = [];
    
    /**
     *
     */
    protected const FILENAME_WITHOUT_COMMA_PATTERN = '/^[^,]+$/';
    
    
    /**
     * AddShopFilesToZipHelper constructor.
     *
     * @param PclZip   $zip
     * @param string[] $fileList every file that should be included in $zip
     * @param string   $shopRoot
     * @param int      $optionFlag
     */
    public function __construct(PclZip $zip, array $fileList, string $shopRoot, int $optionFlag)
    {
        $this->zip        = $zip;
        $this->fileList   = $fileList;
        $this->shopRoot   = $shopRoot;
        $this->optionFlag = $optionFlag;
    }
    
    
    /**
     * @throws RuntimeException
     */
    public function addFilesToZip(): void
    {
        $filesWithComma = $filesWithOutComma = [];
        
        foreach ($this->fileList as $file) {
        
            if (preg_match(self::FILENAME_WITHOUT_COMMA_PATTERN, $file) !== 0) {
                
                $filesWithOutComma[] = $file;
            } else {
                
                $filesWithComma[] = $file;
            }
        }
        
        if (count($filesWithOutComma) > 0) {
    
            $fileList = implode(',', $filesWithOutComma);
            $result   = $this->zip->add($fileList, $this->optionFlag, $this->shopRoot);
            
            if ($result === 0) {
                
                throw new RuntimeException($this->zip->errorInfo());
            }
        }
        
        if (count($filesWithComma) > 0) {
            
            $this->additionalFiles = $filesWithComma;
        }
    }
    
    
    /**
     * @return bool
     */
    public function additionalFilesExists(): bool
    {
        return count($this->additionalFiles) !== 0;
    }
    
    
    /**
     * @return string[]
     */
    public function additionalFiles(): array
    {
        return $this->additionalFiles;
    }
}