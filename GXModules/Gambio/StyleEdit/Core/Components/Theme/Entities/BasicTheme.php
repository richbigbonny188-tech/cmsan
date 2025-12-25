<?php
/*--------------------------------------------------------------------------------------------------
    CurrentTheme.php 2019-10-21
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Core\Components\Theme\Entities;

use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\BasicThemeInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;

/**
 * Class CurrentTheme
 * @package Gambio\StyleEdit\Core\Components\Theme\Entities
 */
class BasicTheme implements BasicThemeInterface, CurrentThemeInterface, SingletonStrategyInterface
{
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var BasicThemeInterface
     */
    protected   $parent;
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * CurrentTheme constructor.
     *
     * @param string              $id
     * @param BasicThemeInterface $parent
     */
    public function __construct(string $id, ?BasicThemeInterface $parent = null)
    {
        $this->id = $id;
        $this->parent = $parent;
    }
    
    
    /**
     * @inheritDoc
     */
    public function parent(): ?BasicThemeInterface
    {
        return $this->parent;
    }
}