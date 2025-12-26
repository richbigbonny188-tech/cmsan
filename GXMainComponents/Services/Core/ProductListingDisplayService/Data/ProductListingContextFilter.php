<?php
/*--------------------------------------------------------------
   ProductListingContextMapper.php 2023-05-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data;

/**
 * Class ProductListingContextMapper
 *
 * @package Gambio\MainComponents\Services\Core\ProductListingDisplayService\Data
 */
class ProductListingContextFilter
{
    /**
     * @param array $session
     *
     * @return array
     */
    public function filter(array $session): array
    {
        $result = [];

        $this->add('tpl', $result, $session);
        $this->add('language', $result, $session);
        $this->add('languages_id', $result, $session);
        $this->add('language_code', $result, $session);
        $this->add('currency', $result, $session);
        $this->add('customers_status', $result, $session);
        $this->add('customer_b2b_status', $result, $session);
        $this->add('customer_country_id', $result, $session);
        $this->add('customer_zone_id', $result, $session);
        $this->add('customer_country_iso', $result, $session);
        $this->add('account_type', $result, $session);

        if (isset($session['tracking']['http_referer'])) {

            $result['http_referer'] = $session['tracking']['http_referer'];
        }

        return $result;
    }


    /**
     * @param mixed $key
     * @param array $result
     * @param array $session
     *
     * @return void
     */
    private function add(mixed $key, array &$result, array $session): void
    {
        if (isset($session[$key])) {

            $result[$key] = $session[$key];
        }
    }
}