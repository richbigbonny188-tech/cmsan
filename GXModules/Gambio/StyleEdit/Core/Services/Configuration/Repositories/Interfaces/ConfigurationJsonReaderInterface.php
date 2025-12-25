<?php
/* --------------------------------------------------------------
  ConfigurationJsonReaderInterface.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Repositories\Interfaces;

use Gambio\StyleEdit\Core\Services\Configuration\Exceptions\ConfigurationJsonNotFoundException;
use stdClass;

/**
 * Interface ConfigurationJsonReaderInterface
 */
interface ConfigurationJsonReaderInterface
{
    /**
     * @param string $themeId
     *
     * @return array
     * @throws ConfigurationJsonNotFoundException
     */
    public function getSettingsById(string $themeId): array;
}