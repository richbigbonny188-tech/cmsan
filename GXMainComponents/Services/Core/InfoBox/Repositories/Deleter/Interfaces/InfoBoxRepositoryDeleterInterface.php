<?php

/* --------------------------------------------------------------
   InfoBoxRepositoryDeleterInterface.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interfaces InfoBoxDeleterInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxRepositoryDeleterInterface
{
    /**
     * Deletes messages by source.
     *
     * @param StringType $source Source.
     */
    public function deleteBySource(StringType $source);
    
    
    /**
     * Deletes a message by ID.
     *
     * @param IdType $id Message ID.
     */
    public function deleteById(IdType $id);
    
    
    /**
     * Deletes a message based  on its identifier
     *
     * @param StringType $identifier Message identifier
     */
    public function deleteByIdentifier(StringType $identifier);
}
