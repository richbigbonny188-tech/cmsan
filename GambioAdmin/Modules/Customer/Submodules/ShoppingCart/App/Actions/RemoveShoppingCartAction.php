<?php
/*--------------------------------------------------------------
   RemoveShoppingCartAction.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\Services\ShoppingCartWriteService;
use Gambio\Admin\Modules\ShoppingCart\App\Actions\CustomerDoesNotExistException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * @codeCoverageIgnore
 */
class RemoveShoppingCartAction
{
    /**
     * @var ShoppingCartWriteService
     */
    private ShoppingCartWriteService $cartWriter;
    
    
    /**
     * Constructor
     */
    public function __construct(ShoppingCartWriteService $cartWriter)
    {
        $this->cartWriter = $cartWriter;
    }
    
    
    /**
     * Action Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $customerId = (int)$request->getAttribute('customerId');
            
            $this->cartWriter->removeShoppingCart($customerId);
            
            return $response->withStatus(200);
        } catch (CustomerDoesNotExistException $e) {
            return $response->withStatus(404);
        }
    }
}