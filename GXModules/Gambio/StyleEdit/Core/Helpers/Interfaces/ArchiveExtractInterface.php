<?php
/* --------------------------------------------------------------
  ArchiveHelperInterface.php 2019-12-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Helpers\Interfaces;

/**
 * Interface ArchiveHelperInterface
 * @package Gambio\StyleEdit\Core\Helpers\Interfaces
 */
interface ArchiveExtractInterface
{
    /**
     * @param        $resourceFile
     * @param string $extractPath
     *
     * @return string
     */
    public function extractArchive($resourceFile, string $extractPath) : string;
}