<?php
/* --------------------------------------------------------------
   DeleteOptionValuesAction.php 2021-10-20
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
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteOptionValuesAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class DeleteOptionValuesAction
{
    /**
     * @var OptionWriteService
     */
    private $writeService;
    
    /**
     * @var OptionReadService
     */
    private $readService;
    
    /**
     * @var OptionFactory
     */
    private $factory;
    
    
    /**
     * DeleteOptionValuesAction constructor.
     *
     * @param OptionWriteService $writeService
     * @param OptionReadService  $readService
     * @param OptionFactory      $factory
     */
    public function __construct(
        OptionWriteService $writeService,
        OptionReadService $readService,
        OptionFactory $factory
    ) {
        $this->writeService = $writeService;
        $this->readService  = $readService;
        $this->factory      = $factory;
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
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->readService->getOptionById($optionId);
        } catch (OptionDoesNotExistException $e) {
            return $response->withStatus(404);
        }
        
        $optionValueIds = [];
        if ($request->getAttribute('optionValueIds') !== null) {
            foreach (explode(',', $request->getAttribute('optionValueIds')) as $id) {
                $optionValueIds[] = $this->factory->createOptionValueId((int)$id);
            }
        }
        
        $option->removeValues(...$optionValueIds);
        try {
            $this->writeService->storeOptions($option);
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