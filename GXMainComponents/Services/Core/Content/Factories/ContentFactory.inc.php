<?php

/* --------------------------------------------------------------
   ContentFactory.inc.php 2019-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentFactory
 *
 * This class represents a factory for content subjects
 *
 * @category   System
 * @package    Content
 */
class ContentFactory implements ContentFactoryInterface
{
    /**
     * Return the info element content entity builder
     *
     * @return InfoElementContentBuilder
     */
    public static function newInfoElementContent(): InfoElementContentBuilder
    {
        return InfoElementContentBuilder::create();
    }
    
    
    /**
     * Return the info page content entity builder
     *
     * @return InfoPageContentBuilder
     */
    public static function newInfoPageContent(): InfoPageContentBuilder
    {
        return InfoPageContentBuilder::create();
    }
    
    
    /**
     * Return the link page content entity builder
     *
     * @return LinkPageContentBuilder
     */
    public static function newLinkPageContent(): LinkPageContentBuilder
    {
        return LinkPageContentBuilder::create();
    }
}