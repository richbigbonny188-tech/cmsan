<?php
/*--------------------------------------------------------------------------------------------------
    AbstractSaveCommand.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\Options\Commands;

use Exception;
use Gambio\StyleEdit\Core\Command\CommandInterface;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;
use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class BasicOptionSaveAction
 * @package Gambio\StyleEdit\Core\Options
 */
abstract class AbstractSaveCommand implements CommandInterface
{
    /**
     * @var AbstractOption
     */
    protected $option;
    /**
     * @var SettingsRepository
     */
    private $configurationRepository;


    /**
     * Execute the command
     */
    abstract public function execute(): void;


    /**
     * Execute the command
     */
    abstract public function rollback(): void;


    /**
     * @param OptionInterface $option
     */
    public function setOption(OptionInterface $option): void
    {
        $this->option = $option;
    }


    /**
     * @throws Exception
     */
    protected function saveOption(): void
    {
        $this->configurationRepository()->saveOptionToConfiguration($this->option);
    }


    /**
     * @param SettingsRepository $configurationRepository
     */
    public function setConfigurationRepository(SettingsRepository $configurationRepository): void
    {
        $this->configurationRepository = $configurationRepository;
    }


    /**
     * @return SettingsRepository
     * @throws Exception
     */
    protected function configurationRepository(): SettingsRepository
    {
        if ($this->configurationRepository === null) {
            $this->configurationRepository = SingletonPrototype::instance()->get(SettingsRepository::class);
        }

        return $this->configurationRepository;
    }


}