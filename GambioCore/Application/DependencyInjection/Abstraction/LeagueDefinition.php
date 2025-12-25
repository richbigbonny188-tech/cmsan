<?php
/* --------------------------------------------------------------
 LeagueDefinition.php 2022-05-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\DependencyInjection\Abstraction;

use Gambio\Core\Application\DependencyInjection\Definition;
use League\Container\Definition\DefinitionInterface;

/**
 * Class LeagueDefinition
 *
 * @package Gambio\Core\Application\DependencyInjection\Abstraction
 */
class LeagueDefinition implements Definition
{
    /**
     * @var DefinitionInterface
     */
    private $internal;
    
    
    /**
     * LeagueDefinition constructor.
     *
     * @param DefinitionInterface $internal
     */
    public function __construct(DefinitionInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addArgument($arg): Definition
    {
        $this->internal->addArgument($arg);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addArguments(array $args): Definition
    {
        $this->internal->addArguments($args);
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): Definition
    {
        $this->internal->setAlias($alias);
        
        return $this;
    }
}