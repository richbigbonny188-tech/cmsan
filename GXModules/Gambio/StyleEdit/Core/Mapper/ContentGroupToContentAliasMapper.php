<?php
/* --------------------------------------------------------------
  ContentGroupToContentAliasMapper.php 2019-12-13
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Mapper;

use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\Core\Mapper\Exceptions\AliasNotFoundException;

/**
 * Class ContentGroupToContentAliasMapper
 * @package Gambio\StyleEdit\Core\Mapper
 */
class ContentGroupToContentAliasMapper implements SingletonStrategyInterface
{
    /**
     * @var string[]
     */
    protected $map = [];
    
    
    /**
     * @param int    $contentGroup
     * @param string $contentAlias
     */
    public function addAlias(int $contentGroup, string $contentAlias): void
    {
        if (array_key_exists($contentGroup, $this->map) === false) {
            
            $this->map[$contentGroup] = $contentAlias;
        }
    }
    
    
    /**
     * @param int $contentGroup
     *
     * @return string
     * @throws AliasNotFoundException
     */
    public function getAlias(int $contentGroup): string
    {
        if (!array_key_exists($contentGroup, $this->map)) {
        
            throw new AliasNotFoundException($contentGroup);
        }
        
        return $this->map[$contentGroup];
    }
    
    
}