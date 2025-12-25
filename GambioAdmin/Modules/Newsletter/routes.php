<?php
/*--------------------------------------------------------------
   routes.php 2022-05-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Admin\Modules\Newsletter\App\Actions\Json\UpdateNewsletterSubscriptionAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/newsletter-subscription', UpdateNewsletterSubscriptionAction::class);
};
