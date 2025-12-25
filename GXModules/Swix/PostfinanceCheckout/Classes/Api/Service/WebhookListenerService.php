<?php
/*--------------------------------------------------------------------------------------------------
    WebhookListenerService.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace SwixPostfinanceCheckout\Service;

class WebhookListenerService
{
    protected $apiClient;

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function search($spaceId, $query)
    {
        $response = $this->apiClient->call('/webhook-listener/search', 'POST', [
            'spaceId' => $spaceId,
        ], $query);

        return $response;
    }

    public function create($spaceId, $data)
    {
        $response = $this->apiClient->call('/webhook-listener/create', 'POST', [
            'spaceId' => $spaceId,
        ], $data);

        return $response;
    }

    public function update($spaceId, $data)
    {
        $response = $this->apiClient->call('/webhook-listener/update', 'POST', [
            'spaceId' => $spaceId,
        ], $data);

        return $response;
    }
}

