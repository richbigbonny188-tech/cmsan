<?php
/*--------------------------------------------------------------
   FetchAllCountriesAction.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\App\Actions;

use Gambio\Admin\Modules\Country\Services\CountryReadService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FetchAllCountriesAction
 *
 * @package Gambio\Admin\Modules\Country\App\Actions
 * @codeCoverageIgnore
 */
class FetchAllCountriesAction
{
    private CountryReadService $service;
    private int                $languageId;
    
    
    /**
     * @param CountryReadService $service
     * @param UserPreferences    $preferences
     */
    public function __construct(
        CountryReadService $service,
        UserPreferences $preferences
    ) {
        $this->service    = $service;
        $this->languageId = $preferences->languageId();
    }
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $response->withJson($this->service->getCountries($this->languageId)->toArray());
    }
}