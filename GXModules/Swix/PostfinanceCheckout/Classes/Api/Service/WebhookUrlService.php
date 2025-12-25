<?php
/*--------------------------------------------------------------------------------------------------
    WebhookUrlService.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace SwixPostfinanceCheckout\Service;

class WebhookUrlService
{
    protected $apiClient;

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function search($spaceId, $query)
    {
        $response = $this->apiClient->call('/webhook-url/search', 'POST', [
            'spaceId' => $spaceId,
        ], $query);

        return $response;
    }

    public function create($spaceId, $data)
    {
        $response = $this->apiClient->call('/webhook-url/create', 'POST', [
            'spaceId' => $spaceId,
        ], $data);

        return $response;
    }

    public function update($spaceId, $data)
    {
        $response = $this->apiClient->call('/webhook-url/update', 'POST', [
            'spaceId' => $spaceId,
        ], $data);

        return $response;
    }
}

