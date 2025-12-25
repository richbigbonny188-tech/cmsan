<?php
/* --------------------------------------------------------------
  StyleEdit3ConfigurationFactoryInterface.php 2019-09-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Factories\Interfaces;

use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;
use StyleEdit3ConfigurationNotFoundException;

/**
 * Interface StyleEdit3ConfigurationFactoryInterface
 */
interface StyleEdit3ConfigurationFactoryInterface
{
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     * @throws StyleEdit3ConfigurationNotFoundException
     */
    public function createForTheme(string $path): StyleEdit3ConfigurationInterface;
    
    
    /**
     * @param string $path
     *
     * @return StyleEdit3ConfigurationInterface
     * @throws StyleEdit3ConfigurationNotFoundException
     */
    public function createForTemplate(string $path): StyleEdit3ConfigurationInterface;
}