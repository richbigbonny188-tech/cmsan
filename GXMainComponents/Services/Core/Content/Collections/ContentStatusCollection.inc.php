<?php

/* --------------------------------------------------------------
   ContentHeadingCollection.inc.php 2021-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentStatusCollection
 *
 * This class represents a collection of localized content headings
 *
 * @category   System
 * @package    Content
 */
class ContentStatusCollection extends AbstractLocalizedContentAttributeCollection
{
    /**
     * Return the valid item type
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return ContentStatus::class;
    }
}
