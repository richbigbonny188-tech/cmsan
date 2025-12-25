<?php

/* --------------------------------------------------------------
   InfoPageContentCollection.inc.php 2019-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoPageContentCollection
 *
 * This class represents a collection of info page content entities
 *
 * @category   System
 * @package    Content
 */
class InfoPageContentCollection extends AbstractCollection
{
    /**
     * Return the valid item type
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return InfoPageContent::class;
    }
}