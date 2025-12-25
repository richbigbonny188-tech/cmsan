<?php
/* --------------------------------------------------------------
   CurrencyRatesCronjobDependencies.inc.php 2022-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class CurrencyRatesCronjobDependencies extends AbstractCronjobDependencies
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return [];
    }
}