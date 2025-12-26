<?php

/* --------------------------------------------------------------
   ContentAttributesInterface.inc.php 2021-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentAttributesInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentAttributesInterface
{
    /**
     * Return the content type
     *
     * @return string
     */
    public function type(): ?string;
    
    
    /**
     * Return the content position
     *
     * @return string
     */
    public function position(): ?string;
    
    
    /**
     * Return whether the content status
     *
     * @return ContentStatusCollection
     */
    public function status(): ?ContentStatusCollection;
    
    
    /**
     * Return the localized content titles
     *
     * @return ContentTitleCollection
     */
    public function titles(): ?ContentTitleCollection;
    
}
