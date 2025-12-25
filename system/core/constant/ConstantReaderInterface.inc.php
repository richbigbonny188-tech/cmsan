<?php
/* --------------------------------------------------------------
   ConstantReaderInterface.inc.php 2019-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface ConstantReaderInterface
{
    const UNDEFINED_VALUE = '999xx999xxx999xxx999xx999xxx';

    public function valueOf($constant, $default_value = ConstantReaderInterface::UNDEFINED_VALUE);

}