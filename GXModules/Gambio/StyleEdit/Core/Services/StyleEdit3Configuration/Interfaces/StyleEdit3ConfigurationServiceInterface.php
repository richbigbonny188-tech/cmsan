<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationServiceInterface.php 2019-09-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Interfaces;

use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;

/**
 * Interface StyleEdit3ConfigurationServiceInterface
 */
interface StyleEdit3ConfigurationServiceInterface
{
    /**
     * @return StyleEdit3ConfigurationInterface[]
     */
    public function configurations(): array;
    
    
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     */
    public function getConfigurationByPath(string $path): StyleEdit3ConfigurationInterface;
}