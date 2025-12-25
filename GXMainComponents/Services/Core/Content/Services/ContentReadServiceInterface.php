<?php
/* --------------------------------------------------------------
  ContentReadServiceInterface.php 2019-07-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ContentReadServiceInterface
 */
interface ContentReadServiceInterface
{
    /**
     * @param mixed $id id of the Content
     *
     * @return mixed
     * @throws ContentNotFoundException
     */
    public function findById($id);
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface;
    
    
    /**
     * @return InfoElementContent[]
     * @throws ContentNotFoundException
     * @throws UnfinishedBuildException
     */
    public function getAllInfoElements(): array;
    
    
    /**
     * @return InfoPageContent[]
     */
    public function getAllContentPages(): array;
}
