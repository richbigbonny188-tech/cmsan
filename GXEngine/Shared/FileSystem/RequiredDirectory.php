<?php
/*--------------------------------------------------------------------
 RequiredDirectory.php 2020-4-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class RequiredDirectory
 */
class RequiredDirectory extends ExistingDirectory
{
    /**
     * RequiredDirectory constructor.
     *
     * @param $absoluteDirPath
     */
    public function __construct($absoluteDirPath)
    {
        try {
            parent::__construct($absoluteDirPath);
        } catch (InvalidArgumentException $argumentException) {
            // trying to create the directory if it does not exist
            if (!mkdir($absoluteDirPath) && !is_dir($absoluteDirPath)) {
                throw $argumentException;
            }
    
            parent::__construct($absoluteDirPath);
        }
    }
}