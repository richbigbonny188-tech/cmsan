<?php
/*--------------------------------------------------------------------------------------------------
    TransactionService.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace SwixPostfinanceCheckout\Service;

class TransactionService
{
    protected $apiClient;

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function read($spaceId, $transactionId)
    {
        $response = $this->apiClient->call('/transaction/read', 'GET', [
            'spaceId' => $spaceId,
            'id' => $transactionId,
        ]);

        return $response;
    }

    public function create($spaceId, $transaction)
    {
        $response = $this->apiClient->call('/transaction/create', 'POST', [
            'spaceId' => $spaceId,
        ], $transaction);

        return $response;
    }

    public function update($spaceId, $transaction)
    {
        $response = $this->apiClient->call('/transaction/update', 'POST', [
            'spaceId' => $spaceId,
        ], $transaction);

        return $response;
    }

    public function search($spaceId, $query)
    {
        $response = $this->apiClient->call('/transaction/search', 'POST', [
            'spaceId' => $spaceId,
        ], $query);

        return $response;
    }

    public function fetchPaymentMethods($spaceId, $transactionId, $integrationMode)
    {
        $response = $this->apiClient->call('/transaction/fetch-payment-methods', 'GET', [
            'spaceId' => $spaceId,
            'id' => $transactionId,
            'integrationMode' => $integrationMode,
        ]);

        return $response;
    }
}

