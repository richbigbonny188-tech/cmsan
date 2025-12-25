<?php

/* --------------------------------------------------------------
  StaticSeoUrlRepositoryDeleterInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlRepositoryDeleterInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlRepositoryDeleterInterface
{
    /**
     * Deletes a StaticSeoUrl by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $staticSeoUrlId);
}