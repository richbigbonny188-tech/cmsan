<?php
/* --------------------------------------------------------------
   HubPaymentMethodProvider.php 2023-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace GXModules\Gambio\Hub\Admin\Classes\Extensions;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Class HubPaymentMethodProvider
 *
 * @package Extensions
 */
class HubPaymentMethodProvider
{
    /**
     * @param Connection $db
     *
     * @return array
     * @throws Exception
     */
    public static function getPaymentMethods(Connection $db): array
    {
        static $paymentMethods;
        
        if ($paymentMethods !== null) {
            return $paymentMethods;
        }
        
        $paymentMethods = [];
        foreach (self::getModules($db) as $moduleName) {
            $query = "
                SELECT `value`
                FROM `gx_configurations`
                WHERE `key` = 'gm_configuration/MODULE_PAYMENT_GAMBIO_HUB_{$moduleName}_ALIAS'
            ";
            $result = $db->executeQuery($query);
            $text  = (string)$result->fetchAssociative()['value'];
            
            $query = "
                SELECT `gambio_hub_module`
                FROM `orders`
                WHERE `gambio_hub_module` = '{$moduleName}'
                LIMIT 1
            ";
            $result = $db->executeQuery($query);
            $moduleName = $result->fetchAssociative()['gambio_hub_module'] ?? $moduleName;
            
            // if no order exits
            $moduleName = ucfirst($moduleName);
            $moduleName = str_replace([
                                          'Cashflowtech',
                                          'Cashondelivery',
                                          'Easycredit',
                                          'Klarnabanktransfer',
                                          'Klarnapaylater',
                                          'Klarnapaynow',
                                          'Klarnasliceit',
                                          'Moneyorder',
                                          'Paypal',
                                      ], [
                                          'CashFlowTech',
                                          'CashOnDelivery',
                                          'EasyCredit',
                                          'KlarnaBanktransfer',
                                          'KlarnaPaylater',
                                          'KlarnaPaynow',
                                          'KlarnaSliceit',
                                          'MoneyOrder',
                                          'PayPal',
                                      ], $moduleName);
            $moduleName = substr($moduleName, 0, -3) . 'Hub';
            
            $paymentMethods[] = [
                'value' => $moduleName,
                'text'  => $text ? $text . ' (Hub)' : $moduleName,
            ];
        }
        
        return $paymentMethods;
    }
    
    
    /**
     * @param Connection $db
     *
     * @return array
     * @throws Exception
     */
    private static function getModules(Connection $db): array
    {
        $moduleNames = [];
        $query       = 'SELECT `key` FROM `gx_configurations` WHERE `key` LIKE "gm\_configuration/MODULE\_PAYMENT\_GAMBIO\_HUB\_%\_ALIAS"';
        $result      = $db->executeQuery($query);
        $hubAliases  = $result->fetchAllAssociative();
        
        foreach ($hubAliases as $alias) {
            $moduleNames[] = strtolower(str_replace(['gm_configuration/MODULE_PAYMENT_GAMBIO_HUB_', '_ALIAS'],
                                                    ['', ''],
                                                    $alias['key']));
        }
        
        $query  = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "configuration/DOWNLOAD_UNALLOWED_PAYMENT"';
        $result = $db->executeQuery($query); 
        
        $forbiddenDownloadPaymentMethods = $result->fetchAssociative()['value'] ?? '';
        $forbiddenDownloadPaymentMethods = preg_split('/\s*,\s*/', (string)$forbiddenDownloadPaymentMethods);
        
        foreach ($forbiddenDownloadPaymentMethods as $paymentMethod) {
            if (substr($paymentMethod, -3) === 'Hub' && !in_array(strtolower($paymentMethod), $moduleNames, true)) {
                $moduleNames[] = $paymentMethod;
            }
        }
        
        return $moduleNames;
    }
}
