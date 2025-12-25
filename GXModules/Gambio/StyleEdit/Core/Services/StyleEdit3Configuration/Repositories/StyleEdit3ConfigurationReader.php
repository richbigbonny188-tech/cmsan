<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationReader.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories;

use FilesystemAdapter;

/**
 * Class StyleEdit3ConfigurationReader
 */
abstract class StyleEdit3ConfigurationReader
{
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    
    /**
     * StyleEdit3ThemeConfigurationReader constructor.
     *
     * @param FilesystemAdapter $filesystem
     */
    public function __construct(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}