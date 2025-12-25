<?php
/*--------------------------------------------------------------------------------------------------
    ContentScriptFileAttributeInterface.php 2021-08-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


interface ContentScriptFileAttributeInterface
{
    
    /**
     * Return the localized content download files
     *
     * @return ContentScriptFileCollection
     */
    public function scripts(): ?ContentScriptFileCollection;
    
    
    /**
     * Return whether the content is opened in a new tab
     *
     * @return ContentOpenInNewTabStatusCollection|null
     */
    public function openInNewTab(): ?ContentOpenInNewTabStatusCollection;
}