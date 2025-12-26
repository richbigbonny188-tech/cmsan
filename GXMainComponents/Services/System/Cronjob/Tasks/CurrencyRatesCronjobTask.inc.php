<?php
/* --------------------------------------------------------------
   CurrencyRatesCronjobTask.inc.php 2022-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class CurrencyRatesCronjobTask extends AbstractCronjobTask
{
    
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $this->logger->lastRun();
            
            $result = xtc_db_query('SELECT `code` FROM `currencies` WHERE `code` != "' . xtc_db_input(DEFAULT_CURRENCY)
                                   . '"');
            while ($row = xtc_db_fetch_array($result)) {
                $response[$row['code']] = CurrencyRateApiClient::getCurrentRate($row['code']);
                if ($response[$row['code']]) {
                    xtc_db_query('UPDATE `currencies`
                                    SET `value` = ' . (double)$response[$row['code']] . '
                                    WHERE `code` = "' . xtc_db_input($row['code']) . '"');
                    $this->logger->log(['update' => $row['code'] . ' value updated to ' . $response[$row['code']]]);
                } else {
                    $this->logger->log(['error' => 'Could not update curreny rate of ' . $row['code']]);
                }
            }
            
            $this->logger->log(['CronjobTask finished' => date('c')]);
            $this->logger->lastSuccess();
            
            return true;
        };
    }
}