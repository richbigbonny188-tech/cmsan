<?php
/*--------------------------------------------------------------------------------------------------
    LoadUserPreferences.php 2022-09-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


namespace Gambio\Api\Application\Bootstrapper;


use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\UserPreferences;

class LoadUserPreferences implements Bootstrapper
{

    /**
     * @todo Refactor the boot method in order to get the correct $customerId and later the $languageId from the APIv3
     *
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $customerId = 1;
        $languageId = 2;
        $userPreferences = new UserPreferences($customerId, $languageId);
        
        $application->registerShared(UserPreferences::class, $userPreferences);
    }
}