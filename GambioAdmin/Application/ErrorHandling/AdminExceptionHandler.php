<?php
/* --------------------------------------------------------------
 AdminExceptionHandler.php 2021-01-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\ErrorHandling;

use Exception;
use Gambio\Admin\Layout\Renderer\GambioAdminRenderer;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;
use Gambio\Core\TextManager\Services\TextManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;
use function Gambio\Core\Logging\logger;

/**
 * Class AdminExceptionHandler
 *
 * @package Gambio\Admin\Application\ErrorHandling
 */
class AdminExceptionHandler extends ErrorHandler
{
    /**
     * @var GambioAdminRenderer
     */
    private $renderer;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * AdminExceptionHandler constructor.
     *
     * @param CallableResolverInterface $callableResolver
     * @param ResponseFactoryInterface  $responseFactory
     * @param GambioAdminRenderer       $renderer
     * @param TextManager               $textManager
     * @param UserPreferences           $userPreferences
     */
    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        GambioAdminRenderer $renderer,
        TextManager $textManager,
        UserPreferences $userPreferences
    ) {
        parent::__construct($callableResolver, $responseFactory);
        $this->renderer        = $renderer;
        $this->textManager     = $textManager;
        $this->userPreferences = $userPreferences;
    }
    
    
    /**
     * @inheritDoc
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        if (!$exception instanceof HttpException) {
            logger('error-handler', true)->critical($exception->getMessage(), ['exception' => $exception]);
        }
        
        return parent::__invoke($request,
                                $exception,
                                $displayErrorDetails,
                                $logErrors,
                                $logErrorDetails);
    }
    
    
    /**
     * @return ResponseInterface
     */
    protected function respond(): ResponseInterface
    {
        if ($this->exception instanceof HttpNotFoundException) {
            return $this->notFoundResponse();
        }
        
        if ((!$this->exception instanceof HttpException)
            && ($this->exception instanceof Exception || $this->exception instanceof Throwable)) {
            return $this->serverErrorResponse();
        }
        
        return parent::respond();
    }
    
    
    /**
     * @return ResponseInterface
     */
    private function serverErrorResponse(): ResponseInterface
    {
        $data                       = [];
        $data['content_navigation'] = [];
        $data['page_title']         = $this->translate('server_error_page_title', 'http_errors');
        $data['pageTitle']          = $this->translate('server_error_page_title', 'http_errors');
        $data['title']              = $this->translate('server_error_title', 'http_errors');
        
        $data['errorInformation'] = [
            [
                'title' => 'Type',
                'msg'   => get_class($this->exception),
            ],
            [
                'title' => 'Code',
                'msg'   => $this->exception->getCode(),
            ],
            [
                'title' => 'Message',
                'msg'   => $this->exception->getMessage(),
            ],
            [
                'title' => 'File',
                'msg'   => $this->exception->getFile(),
            ],
            [
                'title' => 'Line',
                'msg'   => $this->exception->getLine(),
            ]
        ];
        
        $data['trace']               = $this->exception->getTraceAsString();
        $data['displayErrorDetails'] = $this->displayErrorDetails;
        
        $templateFile = __DIR__ . '/templates/500.html';
        try {
            $template = $this->renderer->render($templateFile, $data);
        } catch (RenderingFailedException $e) {
            $template = var_export($data, true);
        }
        
        $response = $this->responseFactory->createResponse(500);
        $response->getBody()->write($template);
        
        return $response;
    }
    
    
    /**
     * @return ResponseInterface
     * @throws RenderingFailedException
     */
    private function notFoundResponse(): ResponseInterface
    {
        $data = [];
        
        $data['content_navigation'] = [];
        $data['page_title']         = $this->translate('page_not_found_page_title', 'http_errors');
        $data['pageTitle']          = $this->translate('page_not_found_page_title', 'http_errors');
        $data['title']              = $this->translate('page_not_found_title', 'http_errors');
        $data['description']        = $this->translate('page_not_found_description', 'http_errors');
        
        $templateFile = __DIR__ . '/templates/404.html';
        $template     = $this->renderer->render($templateFile, $data);
        
        $response = $this->responseFactory->createResponse($this->statusCode);
        $response->getBody()->write($template);
        
        return $response;
    }
    
    
    /**
     * @param string $phrase
     * @param string $section
     *
     * @return string
     */
    private function translate(string $phrase, string $section): string
    {
        return $this->textManager->getPhraseText($phrase, $section, $this->userPreferences->languageId());
    }
}