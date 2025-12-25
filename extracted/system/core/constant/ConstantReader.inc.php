<?php
/* --------------------------------------------------------------
   ConstantReader.inc.php 2019-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class ConstantReader implements ConstantReaderInterface
{

    public function valueOf($constant, $default_value = ConstantReaderInterface::UNDEFINED_VALUE)
    {
        if (defined($constant)) {
            return constant($constant);
        } elseif ($default_value === ConstantReaderInterface::UNDEFINED_VALUE) {
            return $default_value;
        } else {
            throw new InvalidArgumentException("Undefined constant $constant");
        }
    }
}