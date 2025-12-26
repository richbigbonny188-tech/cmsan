<?php
/* --------------------------------------------------------------
   DeleteOptionsAction.php 2021-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteOptionsAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class DeleteOptionsAction
{
    /**
     * @var OptionWriteService
     */
    private $service;
    
    
    /**
     * DeleteOptionsAction constructor.
     *
     * @param OptionWriteService $service
     */
    public function __construct(OptionWriteService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $ids = [];
        if ($request->getAttribute('optionIds') !== null) {
            foreach (explode(',', $request->getAttribute('optionIds')) as $id) {
                $ids[] = (int)$id;
            }
        }
        
        try {
            $this->service->deleteOptions(...$ids);
        } catch (OperationHasNotBeenPermittedException $e) {
            return $response->withStatus(409)->withJson(['errors' => [[$this->getOptionInUseErrorMessage($e)]]]);
        }
        
        return $response->withStatus(204);
    }
    
    /**
     * @param OperationHasNotBeenPermittedException $exception
     *
     * @return string
     */
    private function getOptionInUseErrorMessage(OperationHasNotBeenPermittedException $exception): string
    {
        $message = 'Option value can\'t be deleted because it is currently used in a %s';
        
        return sprintf($message, preg_match('#ProductVariantsOptionOperationPermitter#', $exception->getMessage()) ? 'variant' : 'product option');
    }
}