<?php

/* --------------------------------------------------------------
   AuthSafetyFileStorage.inc.php 2021-01-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a safety file storage
 */
class AuthSafetyFileStorage
{
    /**
     * File name template
     */
    const FILE_NAME_TEMPLATE = '2fa-{CUSTOMER_ID}';
    
    /**
     * Path to directory
     * @var string
     */
    protected $directoryPath;
    
    
    /**
     * Create instance
     *
     * @param NonEmptyStringType $directory Path to directory
     *
     * @throws AuthSafetyFileStorageDirectoryInvalidException On invalid directory path
     */
    public function __construct(NonEmptyStringType $directory)
    {
        $directoryAsString = $directory->asString();

        if(!file_exists($directoryAsString))
        {
            mkdir($directoryAsString,0777,true);
        }

        if (!is_dir($directoryAsString)) {
            throw new AuthSafetyFileStorageDirectoryInvalidException('Invalid directory path');
        }
        
        $this->directoryPath = $directoryAsString;
    }
    
    
    /**
     * Create safety file for provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return $this Same instance
     */
    public function createFileForCustomer(IdType $customerId)
    {
        $file = $this->filePathForCustomer($customerId);
        
        touch($file);
        
        return $this;
    }
    
    
    /**
     * Return whether the safety file exists
     *
     * @param IdType $customerId Customer ID
     *
     * @return bool Whether the safety file exists
     */
    public function fileExistenceForCustomer(IdType $customerId)
    {
        $file = $this->filePathForCustomer($customerId);
        
        return is_file($file);
    }
    
    
    /**
     * Remove safety file for provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return $this Same instance
     */
    public function removeFileForCustomer(IdType $customerId)
    {
        $file = $this->filePathForCustomer($customerId);
        
        unlink($file);
        
        return $this;
    }
    
    
    /**
     * Return the substituted file path for the provided customer ID
     *
     * @param IdType $customerId Customer ID
     *
     * @return string Substituted file path
     */
    protected function filePathForCustomer(IdType $customerId)
    {
        $id       = (string)$customerId->asInt();
        $fileName = str_replace('{CUSTOMER_ID}', $id, self::FILE_NAME_TEMPLATE);
        
        return ($this->directoryPath . '/' . $fileName);
    }
}