<?php

/* --------------------------------------------------------------
   GroupNotFoundException.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GroupNotFoundException
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Exceptions
 */
class GroupNotFoundException extends Exception
{
    
    /**
     * GroupNotFoundException constructor.
     *
     * @param NonEmptyStringType $pageIdentifier Page identifier that was used to identify group.
     */
    public function __construct(NonEmptyStringType $pageIdentifier)
    {
        parent::__construct('Group by page identifier: ' . $pageIdentifier->asString() . ' not found.');
    }
}
