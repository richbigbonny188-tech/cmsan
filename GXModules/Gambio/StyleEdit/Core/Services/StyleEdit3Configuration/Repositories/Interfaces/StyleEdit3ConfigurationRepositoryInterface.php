<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationRepositoryInterface.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Repositories\Interfaces;

use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;

/**
 * Interface StyleEdit3ConfigurationRepositoryInterface
 */
interface StyleEdit3ConfigurationRepositoryInterface
{
    /**
     * @return StyleEdit3ConfigurationInterface[] paths to a StyleEdit3 json file
     */
    public function configurations(): array;
}