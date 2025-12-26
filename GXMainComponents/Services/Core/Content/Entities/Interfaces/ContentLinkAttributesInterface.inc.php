<?php

/* --------------------------------------------------------------
   ContentLinkAttributesInterface.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentLinkAttributesInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentLinkAttributesInterface
{
    /**
     * Return the localized content links
     *
     * @return ContentLinkCollection
     */
    public function links(): ?ContentLinkCollection;
    
    
    /**
     * Return localized content is opened in a new tab
     *
     * @return ContentOpenInNewTabStatusCollection
     */
    public function openInNewTab(): ?ContentOpenInNewTabStatusCollection;
}