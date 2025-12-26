<?php
/*--------------------------------------------------------------
   AddANewImageToAnOptionValueAction.php 2021-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\OptionReadService as OptionReadServiceInterface;
use Gambio\Admin\Modules\Option\Services\OptionWriteService as OptionWriteServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Throwable;

/**
 * Class AddANewImageToAnOptionValueAction
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class AddANewImageToAnOptionValueAction
{
    /**
     * @var OptionReadServiceInterface
     */
    private $readService;
    
    /**
     * @var OptionWriteServiceInterface
     */
    private $writeService;
    
    
    /**
     * AddANewImageToAnOptionValueAction constructor.
     *
     * @param OptionReadServiceInterface  $readService
     * @param OptionWriteServiceInterface $writeService
     */
    public function __construct(
        OptionReadServiceInterface $readService,
        OptionWriteServiceInterface $writeService
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
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
        $relativePath = $request->getAttribute('relativePath');
        $absolutePath = dirname(__DIR__, 5) . '/images/product_images/option_images/' . $relativePath;
    
        if (($optionId = (int)$request->getAttribute('optionId')) === 0) {
            
            return $response->withStatus(409)->withJson(['errors' => [['optionId can\'t be 0']]]);
        }
    
        if (($optionValueId = (int)$request->getAttribute('optionValueId')) === 0) {
            
            return $response->withStatus(409)->withJson(['errors' => [['optionValueId can\'t be 0']]]);
        }
    
        if (file_exists($absolutePath)) {
        
            return $response->withStatus(409)->withJson([
                                                            'errors' => [
                                                                [
                                                                    'Image with relative path "' . $relativePath
                                                                    . '" does already exist.',
                                                                ],
                                                            ],
                                                        ]);
        }
        
        try {
    
            if (@file_put_contents($absolutePath, $request->getBody()->getContents()) === false) {
                
                return $response->withStatus(500)->withJson([
                                                                'errors' => [
                                                                    [
                                                                        'Could not write file "' . $absolutePath . '".'
                                                                    ]
                                                                ]
                                                            ]);
            }
            
            $option = $this->readService->getOptionById($optionId);
    
            foreach ($option->values() as $value) {
                
                if ($value->id() === $optionValueId) {
                    
                    $option->changeValues($value->withImage($relativePath));
                }
            }
            
            $this->writeService->storeOptions($option);
            
            return $response->withStatus(201);
        
        } catch (Throwable $exception) {
            @unlink($absolutePath);
    
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}