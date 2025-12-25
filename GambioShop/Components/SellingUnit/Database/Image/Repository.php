<?php
/*--------------------------------------------------------------------------------------------------
    RepositoryInterface.php 2020-02-12
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\SellingUnit\Database\Image;

use Gambio\Shop\Language\ValueObjects\LanguageId;
use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEvent;
use Gambio\Shop\SellingUnit\Database\Image\Events\OnImageCollectionCreateEventInterface;
use Gambio\Shop\SellingUnit\Database\Images\Events\OnMainImageCreateEvent;
use Gambio\Shop\SellingUnit\Images\Entities\Interfaces\SellingUnitImageCollectionInterface;
use Gambio\Shop\SellingUnit\Images\Entities\SellingUnitImageCollection;
use product_ORIGIN;
use Psr\EventDispatcher\EventDispatcherInterface;

class Repository
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    
    /**
     * RepositoryInterface constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    
    /**
     * @param ProductId                             $id
     * @param ModifierIdentifierCollectionInterface $modifiers
     *
     * @return mixed
     */
    public function getImagesFor(
        ProductId $id,
        ModifierIdentifierCollectionInterface $modifiers
    ): SellingUnitImageCollectionInterface {
        /**
         * @var OnImageCollectionCreateEventInterface $event
         */
        $event = $this->dispatcher->dispatch(new OnImageCollectionCreateEvent($id, $modifiers));
        
        return $event->builder()->build();
    }
    
    
    /**
     * @param product_ORIGIN                        $product
     * @param ProductId                             $id
     * @param ModifierIdentifierCollectionInterface $modifiers
     *
     * @param LanguageId                            $languageId
     *
     * @return SellingUnitImageCollection
     */
    public function getMainImageFor(
        $product,
        ProductId $id,
        ModifierIdentifierCollectionInterface $modifiers,
        LanguageId $languageId
    ): SellingUnitImageCollectionInterface {
        
        $event = new OnMainImageCreateEvent($product, $id, $modifiers, $languageId);
        $this->dispatcher->dispatch($event);
        
        return $event->builder()->build();
    }
    
}