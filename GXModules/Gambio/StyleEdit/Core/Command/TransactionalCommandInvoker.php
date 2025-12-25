<?php

namespace Gambio\StyleEdit\Core\Command;

use Exception;

/**
 * Class CommandInvoker
 * @package Gambio\StyleEdit\Core
 */
class TransactionalCommandInvoker
{
    protected $executedList = [];
    
    
    /**
     * @param array $commandList
     *
     * @throws \Exception
     */
    public function runInsideTransaction(array $commandList): void
    {
        $this->executedList = [];
        try {
            $this->runCommandList($commandList);
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    
    /**
     * @param array $commandList
     */
    public function runCommandList(array $commandList): void
    {
        foreach ($commandList as $command) {
            $command->execute();
            $this->executedList[] = $command;
        }
    }
    
    
    /**
     *
     */
    public function rollback(): void
    {
        
        foreach ($this->executedList as $command) {
            $command->rollback();
        }
    }
    
    
}