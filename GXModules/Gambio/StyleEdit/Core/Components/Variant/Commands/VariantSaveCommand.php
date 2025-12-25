<?php
/*--------------------------------------------------------------------------------------------------
    VariantSaveCommand.php 2020-04-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Commands;

use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Command\CommandInterface;
use Gambio\StyleEdit\Core\Components\Variant\Entities\VariantOption;
use Gambio\StyleEdit\Core\Components\Variant\Services\VariantService;
use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;

/**
 * Class VariantSaveCommand
 * @package Gambio\StyleEdit\Core\Components\Variant\Commands
 */
class VariantSaveCommand extends AbstractSaveCommand implements CommandInterface, AlwaysNewStrategyInterface
{
    /**
     * @var VariantService
     */
    protected $variantService;
    /**
     * @var SettingsRepository
     */
    private $configurationRepository;


    /**
     * VariantSaveCommand constructor.
     *
     * @param VariantService     $variantService
     * @param SettingsRepository $configurationRepository
     */
    public function __construct(VariantService $variantService, SettingsRepository $configurationRepository)
    {
        $this->variantService = $variantService;
        $this->configurationRepository = $configurationRepository;
    }


    /**
     * Execute the command
     *
     * @throws Exception
     */
    public function execute(): void
    {
        $this->saveOption();
        $this->restoreVariantSettings();
    }


    /**
     * @throws Exception
     */
    protected function restoreVariantSettings()
    {
        if (is_a($this->option, VariantOption::class)
            && $this->variantService->exists($this->option()->id(), $this->option()->value()->id())) {

            $settings = $this->variantService->getSettingsFromVariant(
                $this->option()->id(),
                $this->option()->value()->id()
            );
            $this->configurationRepository->saveJsonConfigurationFrom(...array_values($settings->getArray()));
        }
    }


    /**
     * @return VariantOption
     */
    protected function option(): VariantOption
    {
        return $this->option;
    }


    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
}