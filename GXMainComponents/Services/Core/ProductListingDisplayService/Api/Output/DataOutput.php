<?php
/* --------------------------------------------------------------
  DataOutput.php 2023-03-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output;

/**
 * Interface DataOutput
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Api\Output
 */
interface DataOutput
{
    /**
     * Gets essential display data wrapped in array output.
     *
     * @return array
     */
    public function toArray(): array;
}