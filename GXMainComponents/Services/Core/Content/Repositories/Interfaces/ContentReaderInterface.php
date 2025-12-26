<?php
/* --------------------------------------------------------------
  ContentReaderInterface.php 2019-07-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Interface ContentReaderInterface
 */
interface ContentReaderInterface
{
    /**
     * @param mixed $id id of the Content
     *
     * @return mixed
     * @throws ContentNotFoundException
     */
    public function findById(ContentIdentificationInterface $id): array;
    
    
    /**
     * @param $id
     *
     * @return array
     * @throws UrlRewriteNotFoundException
     */
    public function findUrlRewriteByContentId($id): array;
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function nextContentGroupId(): ContentIdentificationInterface;
    
    
    /**
     * @return array
     * @throws ContentNotFoundException
     */
    public function findAllInfoElements(): array;
    
    
    /**
     * @return array
     */
    public function findAllContentPages(): array;
}
