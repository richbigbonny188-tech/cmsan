<?php
/* --------------------------------------------------------------
  ThemeSettingsReaderInterface.php 2019-08-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces;

use stdClass;

/**
 * Interface ThemeSettingsReaderInterface
 */
interface ThemeSettingsReaderInterface
{
    /**
     * @param string $id
     *
     * @return array
     */
    public function getById(string $id): array;
    
    
    /**
     * @return stdClass[]
     */
    public function getAll(): array;
}