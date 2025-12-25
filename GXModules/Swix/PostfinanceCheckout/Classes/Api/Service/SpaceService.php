<?php
/*--------------------------------------------------------------------------------------------------
    SpaceService.php 2021-04-08
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2021 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace SwixPostfinanceCheckout\Service;

class SpaceService
{
    protected $apiClient;

    public function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function read($spaceId)
    {
        $response = $this->apiClient->call('/space/read', 'GET', [
            'id' => $spaceId,
        ]);

        return $response;
    }
}

