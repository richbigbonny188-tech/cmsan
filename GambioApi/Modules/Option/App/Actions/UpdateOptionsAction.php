<?php
/* --------------------------------------------------------------
   UpdateOptionsAction.php 2021-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Exception;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionDetail;
use Gambio\Admin\Modules\Option\Services\OptionFactory;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Api\Modules\Option\App\OptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateOptionsAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class UpdateOptionsAction
{
    /**
     * @var OptionApiRequestValidator
     */
    private $requestValidator;
    
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
     * UpdateOptionsAction constructor.
     *
     * @param OptionApiRequestValidator $requestValidator
     * @param OptionWriteService        $writeService
     * @param OptionReadService         $readService
     * @param OptionFactory             $factory
     */
    public function __construct(
        OptionApiRequestValidator $requestValidator,
        OptionWriteService $writeService,
        OptionReadService $readService,
        OptionFactory $factory
    ) {
        $this->requestValidator = $requestValidator;
        $this->writeService     = $writeService;
        $this->readService      = $readService;
        $this->factory          = $factory;
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
        $errors = $this->requestValidator->validateOptionPutRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $options = [];
        foreach ($request->getParsedBody() as $index => $optionData) {
            try {
                $details   = array_map([$this, 'mapOptionDetail'], $optionData['details']);
                $type      = $this->factory->createOptionType($optionData['type']);
                $sortOrder = (int)$optionData['sortOrder'];
                
                $option = $this->readService->getOptionById((int)$optionData['id']);
                $option->changeDetails($this->factory->createOptionDetails(...array_values($details)));
                $option->changeType($type);
                $option->changeSortOrder($sortOrder);
                $options[] = $option;
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        $this->writeService->storeOptions(...$options);
        
        return $response->withStatus(204);
    }
    
    
    /**
     * @param array $detailData
     *
     * @return OptionDetail
     */
    private function mapOptionDetail(array $detailData): OptionDetail
    {
        return $this->factory->createOptionDetail($detailData['languageCode'],
                                                  $detailData['label'],
                                                  $detailData['adminLabel'],
                                                  $detailData['description']);
    }
}