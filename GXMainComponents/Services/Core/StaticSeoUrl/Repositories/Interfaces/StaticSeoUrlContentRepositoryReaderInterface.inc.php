<?php

/* --------------------------------------------------------------
  StaticSeoUrlContentRepositoryReaderInterface.inc.php 2017-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface StaticSeoUrlContentRepositoryReaderInterface
 *
 * @category   System
 * @package    StaticSeoUrl
 * @subpackage Interfaces
 */
interface StaticSeoUrlContentRepositoryReaderInterface
{
    /**
     * Returns a StaticSeoUrlContentCollection for the given StaticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlContentCollection All staticSeoUrlContents found by the staticSeoUrl ID as a
     *                                       StaticSeoUrlContentCollection.
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getByStaticSeoUrlId(IdType $staticSeoUrlId);
    
    
    /**
     * Returns a StaticSeoUrlContent instance by the given staticSeoUrlContent ID.
     *
     * @param IdType $staticSeoUrlContentId
     *
     * @return StaticSeoUrlContentInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $staticSeoUrlContentId);
}