<?php
/*--------------------------------------------------------------------------------------------------
    AbstractGroupRepository.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core;

use Gambio\Shop\ProductModifiers\Database\Core\Mappers\Interfaces\GroupMapperInterface;
use Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces\GroupReaderCompositeInterface;
use Gambio\Shop\ProductModifiers\Database\Core\Readers\Interfaces\ModifierReaderCompositeInterface;
use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;
use Gambio\Shop\ProductModifiers\Groups\Repositories\GroupRepositoryInterface;

/**
 * Class AbstractGroupRepository
 * @package Gambio\Shop\ProductModifiers\Database\Core
 */
abstract class AbstractGroupRepository implements GroupRepositoryInterface
{
    /**
     * @var GroupMapperInterface
     */
    protected $groupMapper;
    /**
     * @var GroupReaderCompositeInterface
     */
    protected $groupReaderComposite;
    /**
     * @var ModifierReaderCompositeInterface
     */
    protected $modifierReaderComposite;
    /**
     * @var PresentationMapperInterface
     */
    protected $presentationMapper;
    
    
    /**
     * AbstractProductModifierRepository constructor.
     *
     * @param GroupMapperInterface             $groupMapper
     * @param GroupReaderCompositeInterface    $groupReaderComposite ,
     * @param ModifierReaderCompositeInterface $productModifierReaderComposite
     */
    public function __construct(
        GroupMapperInterface $groupMapper,
        GroupReaderCompositeInterface $groupReaderComposite,
        ModifierReaderCompositeInterface $productModifierReaderComposite
    
    ) {
        $this->groupReaderComposite    = $groupReaderComposite;
        $this->modifierReaderComposite = $productModifierReaderComposite;
        $this->groupMapper             = $groupMapper;
    }
    
}