<?php
/*--------------------------------------------------------------------------------------------------
    callback.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\Core\Logging\LoggerBuilder;

chdir('../../');

define('_VALID_XTC', true);

include('includes/application_top.php');
include('GXModules/Swix/PostfinanceCheckout/Classes/SwixPostfinanceCheckoutBase.inc.php');

$loggerBuilder = \LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
$logger  = $loggerBuilder->omitRequestData()->changeNamespace('swixpostfinancecheckout')->build();

$request = file_get_contents("php://input");
$params = json_decode($request, true);

$logger->info("Callback called with these params:\n" . print_r($params, true));

$swixPostfinanceCheckoutBase = new SwixPostfinanceCheckoutBase();

if (isset($params['listenerEntityId']) && $params['listenerEntityId'] == '1472041829003'
    && isset($params['entityId'])
    && isset($params['spaceId']) && $params['spaceId'] == $swixPostfinanceCheckoutBase->getSpaceId()) {

    try {
        $transaction = $swixPostfinanceCheckoutBase->getApiClient()->getTransactionService()->read($swixPostfinanceCheckoutBase->getSpaceId(), $params['entityId']);
        if ($transaction === false) {
            throw new \Exception('could not read transaction!');
        }
        $metaData = $transaction['metaData'];

        if (isset($metaData['payment_class'])) {

            include_once(DIR_FS_CATALOG . 'includes/modules/payment/' . $metaData['payment_class'] . '.php');

            $paymentClass = new $metaData['payment_class']();
            $paymentClass->callback($transaction);
        }

    } catch(Exception $e) {
        $logger->error($e->getMessage());
    }
}
