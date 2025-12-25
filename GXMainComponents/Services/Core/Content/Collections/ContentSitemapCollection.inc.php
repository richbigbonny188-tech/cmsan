<?php
/* --------------------------------------------------------------
   ContentSitemapCollection.inc.php 2021-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentSitemapCollection
 *
 * This class represents a collection of localized content sitemaps
 *
 * @category   System
 * @package    Content
 */
class ContentSitemapCollection extends AbstractLocalizedContentAttributeCollection
{
    /**
     * Return the valid item type
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return ContentSitemap::class;
    }
}