<?php
/*--------------------------------------------------------------------------------------------------
    OnMainImageCreateEventListener.php 2020-02-17
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\Properties\SellingUnitImages\Database\Listener;

use Gambio\Shop\SellingUnit\Database\Image\Events\OnMainImageCreateEventInterface;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\ImageListIsEmptyException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Exceptions\PropertyDoesNotHaveAnImageListException;
use Gambio\Shop\Properties\SellingUnitImages\Database\Service\ReadServiceInterface;

/**
 * Class OnMainImageCreateEventListener
 * @package Gambio\Shop\Properties\SellingUnitImages\Database\Listener
 */
class OnMainImageCreateEventListener
{
    /**
     * @var ReadServiceInterface
     */
    protected $readService;
    
    
    /**
     * OnMainImageCreateEventListener constructor.
     *
     * @param ReadServiceInterface $readService
     */
    public function __construct(ReadServiceInterface $readService)
    {
        $this->readService = $readService;
    }
    
    
    /**
     * @param OnMainImageCreateEventInterface $event
     */
    public function __invoke(OnMainImageCreateEventInterface $event): void
    {
        $productId  = $event->productId();
        $modifiers  = $event->modifiers();
        $languageId = $event->languageId();
        
        try {
            $image = $this->readService->getMainImageListImage($productId, $modifiers, $languageId);
        } catch (ImageListIsEmptyException | PropertyDoesNotHaveAnImageListException $exception) {
            unset($exception);
            return;
        }
        
        $event->builder()->withImage($image);
    }
}