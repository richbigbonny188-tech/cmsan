<?php

/* --------------------------------------------------------------
   ContentFactoryInterface.inc.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContentFactoryInterface
 *
 * @category   System
 * @package    Content
 * @subpackage Interfaces
 */
interface ContentFactoryInterface
{
    /**
     * Return the info element content entity builder
     *
     * @return InfoElementContentBuilder
     */
    public static function newInfoElementContent(): InfoElementContentBuilder;
    
    
    /**
     * Return the info page content entity builder
     *
     * @return InfoPageContentBuilder
     */
    public static function newInfoPageContent(): InfoPageContentBuilder;
    
    
    /**
     * Return the link page content entity builder
     *
     * @return LinkPageContentBuilder
     */
    public static function newLinkPageContent(): LinkPageContentBuilder;
}