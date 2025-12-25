<?php
/* --------------------------------------------------------------
 routes.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Api\Modules\Withdrawal\App\Actions\CreateWithdrawalsAction;
use Gambio\Api\Modules\Withdrawal\App\Actions\DeleteWithdrawalsAction;
use Gambio\Api\Modules\Withdrawal\App\Actions\FetchAllWithdrawalsAction;
use Gambio\Api\Modules\Withdrawal\App\Actions\FetchSpecificWithdrawalAction;
use Gambio\Api\Modules\Withdrawal\App\Actions\PatchWithdrawalsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/api.php/v3/withdrawals', FetchAllWithdrawalsAction::class);
    $routeCollector->post('/api.php/v3/withdrawals', CreateWithdrawalsAction::class);
    $routeCollector->patch('/api.php/v3/withdrawals', PatchWithdrawalsAction::class);
    $routeCollector->delete('/api.php/v3/withdrawals/{ids:[0-9,]+}', DeleteWithdrawalsAction::class);
    $routeCollector->get('/api.php/v3/withdrawals/{id:[0-9]+}', FetchSpecificWithdrawalAction::class);
};
