<?php
/* --------------------------------------------------------------
   StringUtilityTrait.inc.php 2018-02-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

trait StringUtilityTrait
{
    protected function strlenWrapper($string, $encoding = 'utf-8')
    {
        return function_exists('mb_strlen') ? mb_strlen($string, $encoding) : strlen($string);
    }
}