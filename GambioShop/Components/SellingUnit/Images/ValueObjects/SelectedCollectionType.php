<?php
/**
 * SelectedCollectionType.php 2020-3-30
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Images\ValueObjects;

use Gambio\Shop\SellingUnit\Images\Builders\CollectionBuilderInterface;

/**
 * Class SelectedCollectionType
 * @package Gambio\Shop\SellingUnit\Images\ValueObjects
 */
class SelectedCollectionType
{
    /**
     * @var CollectionBuilderInterface
     */
    protected $selectionBuilderClass;
    
    
    /**
     * SelectedCollectionType constructor.
     *
     * @param CollectionBuilderInterface $selectionBuilderClass
     */
    public function __construct(CollectionBuilderInterface $selectionBuilderClass)
    {
        $this->selectionBuilderClass = $selectionBuilderClass;
    }
    
    
    /**
     * @return CollectionBuilderInterface
     */
    public function value(): CollectionBuilderInterface
    {
        return $this->selectionBuilderClass;
    }
}