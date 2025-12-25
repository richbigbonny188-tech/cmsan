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

class RefundService
{
    protected $apiClient;

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function create($spaceId, $refund)
    {
        $response = $this->apiClient->call('/refund/refund', 'POST', [
            'spaceId' => $spaceId,
        ], $refund);

        return $response;
    }

    public function read($spaceId, $id)
    {

    }

    public function search($spaceId, $query)
    {
        $response = $this->apiClient->call('/refund/search', 'POST', [
            'spaceId' => $spaceId,
        ], $query);

        return $response;
    }
}

