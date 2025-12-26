<?php
/* --------------------------------------------------------------
  ContentManagerOptionValueInterface.php 2019-08-02
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentManager\Interfaces;

use JsonSerializable;

/**
 * Interface ContentManagerOptionValueInterface
 */
interface ContentManagerOptionValueInterface extends JsonSerializable
{
    /**
     * @return string
     */
    public function value(): string;
}