<?php
/*--------------------------------------------------------------
   FilenameStringType.php 2020-06-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

/**
 * Class FilenameStringType
 *
 * @category   System
 * @package    Shared
 * @subpackage FileSystem
 */
class FilenameStringType extends StringType
{
    
    /**
     * FilenameStringType constructor.
     *
     * @param string $filename
     *
     * @throws InvalidArgumentException if $filename contains invalid characters
     */
    public function __construct($filename)
    {
        parent::__construct($filename);
        $this->_validateFilename($filename);
    }
    
    
    /**
     * Validates file name.
     *
     * @param string $filename
     *
     * @return FilenameStringType Same instance for chained method calls.
     * @throws InvalidArgumentException if $filename contains invalid characters
     *
     */
    protected function _validateFilename($filename)
    {
        // backup locale setting
        $originalLocales = explode(';', setlocale(LC_ALL, 0));
        
        // change locale to multibyte character charset allowing characters like umlauts
        // en_US.UTF8 should always be available
        setlocale(LC_ALL, 'en_US.UTF8');
        
        $this->validateFilenameIsNotAPath($filename);
        
        //Recover to the default setting
        //these will be returned by setlocale(LC_ALL, 0), but don't exist anymore.
        $skipConstants = [
            'LC_PAPER',
            'LC_NAME',
            'LC_ADDRESS',
            'LC_TELEPHONE',
            'LC_MEASUREMENT',
            'LC_IDENTIFICATION'
        ];
        
        foreach ($originalLocales as $localeSetting) {
            if (strpos($localeSetting, '=') !== false) {
                list ($category, $locale) = explode('=', $localeSetting);
            } else {
                $category = 'LC_ALL';
                $locale   = $localeSetting;
            }
            
            if (!in_array($category, $skipConstants)) {
                setlocale(constant($category), $locale); //Using strings is deprecated.
            }
        }
        
        return $this;
    }
    
    
    /**
     * @param string $filename
     */
    protected function validateFilenameIsNotAPath(string $filename): void
    {
        if ($filename !== basename($filename)) {
            
            throw new InvalidArgumentException('Filename "' . $filename . '" is not valid');
        }
    }
}