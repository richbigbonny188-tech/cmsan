<?php
/*--------------------------------------------------------------
   routes.php 2021-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Modules\Currency\App\Actions\FetchAllCurrenciesAction;
use Gambio\Api\Modules\Currency\App\Actions\FetchSpecificCurrencyAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    
    $routeCollector->get('/api.php/v3/currencies', FetchAllCurrenciesAction::class);
    $routeCollector->get('/api.php/v3/currencies/{currencyId}', FetchSpecificCurrencyAction::class);
};
