<?php

namespace Gambio\StyleEdit\Core\Components\Option\Commands;

use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;
use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;

/**
 * Class OptionSaveCommand
 * @package Gambio\StyleEdit\Core\Components\Option\Commands
 */
class GroupOptionSaveCommand extends AbstractSaveCommand
{
    
    /**
     * Execute the command
     */
    public function execute(): void
    {
        $this->saveOption();
    }
    
    
    /**
     * Execute the command
     */
    public function rollback(): void
    {
        // TODO: Implement rollback() method.
    }
    
    
    /**
     * @throws \Exception
     */
    protected function saveOption(): void
    {
        foreach ($this->option->getGroupOptions() as $option) {
            $this->configurationRepository()->saveOptionToConfiguration($option);
        }
    }
}