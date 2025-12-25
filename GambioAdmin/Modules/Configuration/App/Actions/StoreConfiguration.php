<?php
/* --------------------------------------------------------------
 StoreConfiguration.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Class StoreConfiguration
 *
 * @package Gambio\Admin\Modules\Configuration\App\Actions
 */
class StoreConfiguration extends AbstractAction
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * StoreConfiguration constructor.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $configurations = $request->getParsedBody()['configurations'] ?? [];
            Assert::isMap($configurations);
            
            foreach ($configurations as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $languageCode => $languageValue) {
                        $this->configurationService->saveLanguageDependent($key, $languageCode, $languageValue);
                    }
                } else {
                    $this->configurationService->save($key, $value);
                }
            }
            
            $this->configurationService->saveBulk($configurations);
            
            return $response->withJson(['success' => true]);
        } catch (Throwable $exception) {
            return $response->withJson(['success' => false, 'error' => $exception->getMessage()]);
        }
    }
}