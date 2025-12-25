<?php

namespace Gambio\StyleEdit\Core\Components\Option\Commands;

use Gambio\StyleEdit\Core\Options\Commands\AbstractSaveCommand;

/**
 * Class OptionSaveCommand
 * @package Gambio\StyleEdit\Core\Components\Option\Commands
 */
class OptionSaveCommand extends AbstractSaveCommand
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
}