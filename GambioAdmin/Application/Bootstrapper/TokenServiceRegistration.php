<?php
/* --------------------------------------------------------------
 TokenServiceRegistration.php 2022-07-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Admin\Application\Token\TokenService;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\SecurityToken;
use Gambio\Admin\Application\Token\Exceptions\SecurityTokenNotFoundException;

use function Gambio\Core\Application\env;

/**
 * Class TokenServiceRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class TokenServiceRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $securityToken = $this->getSecurityToken();

        $application->registerShared(SecurityToken::class)->addArgument($securityToken);
        $application->registerShared(TokenService::class)
            ->addArgument(SecurityToken::class)
            ->addArgument(Path::class);
    }


    /**
     * @return string
     * @throws SecurityTokenNotFoundException
     * @deprecated refs #69845: method is only for compatibility support for shops older than v4.4 ()
     * @todo       refs #69857: remove exception from here and refactor TokenService
     */
    private function getSecurityToken(): string
    {
        require_once __DIR__ . '/../../../GambioCore/Application/env.php';

        $securityToken = env('APP_SECURITY_TOKEN');

        if ($securityToken === null) {
            $updateNeededFlagExists = file_exists(dirname(__DIR__, 3) . "/cache/update_needed.flag");
            if (!$updateNeededFlagExists) {
                throw new SecurityTokenNotFoundException(
                    'The config/.env.php does not contain the security token APP_SECURITY_TOKEN.'
                );
            }
            $securityToken = bin2hex(random_bytes(16));
        }

        return $securityToken;
    }
}