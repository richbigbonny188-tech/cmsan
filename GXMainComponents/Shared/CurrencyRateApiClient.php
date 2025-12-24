<?php
/* --------------------------------------------------------------
   CurrencyRateApiClient.php 2022-10-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class CurrencyRateApiClient
{
    /**
     * @param $to
     * @param $base
     *
     * @return false|mixed
     */
    public static function getCurrentRate($to, $base = DEFAULT_CURRENCY)
    {
        static $exchangeRates;
        
        if (!empty($exchangeRates[$base][$to])) {
            return $exchangeRates[$base][$to];
        }
        
        // Currency converter. https://apilayer.com/marketplace/exchangerates_data-api
        $url    = 'https://api.apilayer.com/exchangerates_data/latest?base=' . $base;
        $apiKey = gm_get_conf('APILAYER_API_KEY') ? : '';
        
        if (empty($apiKey)) {
            return false;
        }
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/plain',
                'apikey: ' . $apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        
        $jsonResponse = json_decode(curl_exec($ch), true);
        
        if (isset($jsonResponse['rates'])) {
            $exchangeRates[$base] = $jsonResponse['rates'];
        }
        
        return $jsonResponse['rates'][$to] ?? false;
    }
}