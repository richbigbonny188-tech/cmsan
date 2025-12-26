<?php

/* --------------------------------------------------------------
   LocalizedContentAttributeInterface.inc.inc.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface LocalizedContentAttributeInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface LocalizedContentAttributeInterface
{
    /**
     * Return the content
     *
     * @return string
     */
    public function content(): string;
    
    
    /**
     * Return the language code
     *
     * @return string
     */
    public function languageCode(): string;
}