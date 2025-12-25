<?php
/* --------------------------------------------------------------
  StyleEdit3ToStyleEdit4ConverterInterface.php 2019-09-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Converter\Interfaces;

use Gambio\StyleEdit\Core\Services\Configuration\Entities\Configuration;
use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\ConfigurationInterface;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\Entities\Interfaces\StyleEdit3ConfigurationInterface;

/**
 * Interface StyleEdit3ToStyleEdit4ConverterInterface
 */
interface StyleEdit3ToStyleEdit4ConverterInterface
{
    /**
     * @param StyleEdit3ConfigurationInterface $styleEdit3Configuration
     *
     * @return ConfigurationInterface
     */
    public function convert(StyleEdit3ConfigurationInterface $styleEdit3Configuration): ConfigurationInterface;
}