<?php

/* --------------------------------------------------------------
  SlideImageAreaRepositoryWriterInterface.inc.php 2016-10-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageAreaRepositoryWriterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageAreaRepositoryWriterInterface
{
    /**
     * Stores a SlideImageArea to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return int ID of inserted slide image area or the used ID if the slide image area had an ID already.
     */
    public function store(IdType $slideImageId, SlideImageAreaInterface $slideImageArea);
}