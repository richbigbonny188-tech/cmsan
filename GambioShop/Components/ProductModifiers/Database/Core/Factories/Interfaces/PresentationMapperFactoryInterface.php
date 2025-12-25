<?php
/*--------------------------------------------------------------------------------------------------
    PresentationMapperFactoryInterface.php 2020-01-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\Factories\Interfaces;

use Gambio\Shop\ProductModifiers\Database\Presentation\Mappers\Interfaces\PresentationMapperInterface;

/**
 * Class PresentationMapperInterface
 * @package Gambio\Shop\ProductModifiers\Database\Core\Mappers
 */
interface PresentationMapperFactoryInterface
{
    /**
     * Methods responsible for creating the mapper chain with all the elements
     * @return PresentationMapperInterface
     */
    public function createMapperChain(): PresentationMapperInterface;
}