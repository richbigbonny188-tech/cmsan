<?php

/* --------------------------------------------------------------
   ContentUrlKeywordsCollection.inc.php 2019-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentUrlKeywordsCollection
 *
 * This class represents a collection of localized content URL keywords
 *
 * @category   System
 * @package    Content
 */
class ContentUrlKeywordsCollection extends AbstractLocalizedContentAttributeCollection
{
    /**
     * Return the valid item type
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return ContentUrlKeywords::class;
    }
}