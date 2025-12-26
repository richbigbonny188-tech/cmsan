<?php
/*--------------------------------------------------------------
   DeleteOptionValuesAction.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteOptionValuesAction
 * @package Gambio\Admin\Modules\Option\App\Actions\Json
 */
class DeleteOptionValuesAction extends AbstractAction
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
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->readService->getOptionById($optionId);
        } catch (OptionDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
    
        $optionValueIds = [];
        if ($request->getAttribute('optionValueIds') !== null) {
            
            $optionValueIds = explode(',', $request->getAttribute('optionValueIds'));
            $optionValueIds = array_map('intval', $optionValueIds);
            $optionValueIds = array_map([$this->factory, 'createOptionValueId'], $optionValueIds);
        }
        
        $option->removeValues(...$optionValueIds);
        
        try {
            $this->writeService->storeOptions($option);
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}