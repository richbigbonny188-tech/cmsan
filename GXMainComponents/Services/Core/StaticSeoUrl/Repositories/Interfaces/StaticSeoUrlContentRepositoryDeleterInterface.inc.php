<?php

/* --------------------------------------------------------------
  StaticSeoUrlContentRepositoryDeleterInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlContentRepositoryDeleterInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlContentRepositoryDeleterInterface
{
    /**
     * Deletes a StaticSeoUrlContent by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $staticSeoUrlContentId);
}