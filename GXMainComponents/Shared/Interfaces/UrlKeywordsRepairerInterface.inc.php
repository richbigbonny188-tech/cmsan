<?php
/* --------------------------------------------------------------
   UrlKeywordsRepairerInterface.inc.php 2018-04-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface UrlKeywordsRepairerInterface
 *
 * @category   System
 * @package    Shared
 * @subpackage Interfaces
 */
interface UrlKeywordsRepairerInterface
{
    /**
     * @param string $type all|products|categories|contents
     * @param int    $p_id
     */
    public function repair($type = 'all', $p_id = 0);
}