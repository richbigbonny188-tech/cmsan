<?php

/* --------------------------------------------------------------
   LocalizedContentAttributeCollectionInterface.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LocalizedContentAttributeCollectionInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface LocalizedContentAttributeCollectionInterface
{
    /**
     * Return an item by its language code
     *
     * @param LanguageCode $languageCode Language code
     *
     * @return LocalizedContentAttributeInterface
     */
    public function itemByLanguageCode(LanguageCode $languageCode): LocalizedContentAttributeInterface;
    
    
    /**
     * Return true if a language code exists at the collection
     *
     * @param LanguageCode $languageCode Language code
     *
     * @return bool
     */
    public function hasLanguageCode(LanguageCode $languageCode): bool;
    
    
    /**
     * @return LocalizedContentAttributeInterface
     */
    public function getLastItem(): LocalizedContentAttributeInterface;
}
