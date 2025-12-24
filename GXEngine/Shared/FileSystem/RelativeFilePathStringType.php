<?php
/*--------------------------------------------------------------
   RelativeFilePathStringType.php 2020-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class RelativeFilePathStringType
 */
class RelativeFilePathStringType extends FilenameStringType
{
    /**
     * @inheritDoc
     */
    protected function validateFilenameIsNotAPath(string $filename): void
    {
        // overwriting existing method because
        // no exception should be thrown if the
        // filename is a path
        if (preg_match('#^/#', $filename)) {
    
            throw new RuntimeException('Absolute paths are not allowed in ' . static::class);
        }
    }
    
    
    /**
     * @param string $filename
     */
    protected function _validateFilename($filename): void
    {
        parent::_validateFilename($filename);
        
        if (preg_match('#\.\.[/\\\\]#', $filename) === 1) {
        
            throw new RuntimeException('Navigating to a toplevel directory in a ' . __CLASS__ . ' is prohibited.');
        }
    }
}