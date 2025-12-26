<?php
/* --------------------------------------------------------------
   OrderShippingOptionCollection.inc.php 2021-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

MainFactory::load_class('AbstractCollection');

class OrderShippingOptionCollection extends AbstractCollection
{
    
    protected function _getValidType()
    {
        return OrderShippingOption::class;
    }
}
