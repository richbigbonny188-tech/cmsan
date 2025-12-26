<?php
/*--------------------------------------------------------------
   GetShoppingCartAction.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions;

use DateTimeInterface;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartReadService;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * @codeCoverageIgnore
 */
class GetShoppingCartAction
{
    /**
     * @var ShoppingCartReadService
     */
    private ShoppingCartReadService $cartReader;
    private int                     $languageId;
    private string                  $datetimeFormat;
    
    
    /**
     * @param ShoppingCartReadService $cartReader
     * @param UserPreferences         $preferences
     */
    public function __construct(
        ShoppingCartReadService $cartReader,
        UserPreferences $preferences
    ) {
        $this->cartReader     = $cartReader;
        $this->languageId     = $preferences->languageId();
        $this->datetimeFormat = DateTimeInterface::ATOM;
    
    }
    
    
    /**
     * Action Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId   = (int)$request->getAttribute('customerId');
            $shoppingCart = $this->cartReader->getShoppingCart($customerId, $this->languageId);
            $data         = $shoppingCart->toArray($this->datetimeFormat);
            
            return $response->withJson($data);
        } catch (CustomerDoesNotExistException $e) {
            return $response->withStatus(404);
        }
    }
}