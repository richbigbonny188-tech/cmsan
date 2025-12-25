<?php
/* --------------------------------------------------------------
   api.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

if (strpos($_SERVER['REQUEST_URI'], '/api.php/v3') !== false) {
    require __DIR__ . '/api_v3.php';
    exit;
}

/**
 * Gambio GX2 - API (implemented with Slim Framework)
 *
 * @link http://www.slimframework.com
 *
 * Hit this file directly with new requests and it will route them to their corresponding API
 * controllers. Controller files reside inside the "GXEngine/Controllers/Api" directory and are
 * separated by version. This separation enables the addition of newer API versions in the future.
 *
 * Since v2 the shop API is RESTful and that means that it supports a variety of HTTP methods
 * in order to implement a semantic interface for client developers. You can use one of the GET,
 * POST, PUT, DELETE, PATCH, HEAD, OPTIONS methods in your controller classes. Check the
 * "HttpApiV2Controller" class for more information on how to create your own controller.
 *
 * @link http://en.wikipedia.org/wiki/Representational_state_transfer
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
 *
 * It is important that each API version is able to route the controllers differently because
 * the codebase will be more flexible and easy to maintain. Expand the current file with new
 * controller-routing rules for future versions.
 *
 * You can generate detailed API documentation through ApiDoc. It is a NodeJS command line tool
 * that parses specific DocBlock comments and creates rich content output. It's always preferable
 * that API methods are well-documented so that is easier for external developers to use them.
 *
 * @link http://apidocjs.com
 *
 * Version 2.0.0 of the API uses HTTP Basic Authentication and that means that authorization
 * credentials are transferred over the wire. Always use HTTPS when accessing the API.
 *
 * http://en.wikipedia.org/wiki/Basic_access_authentication
 */

use Slim\Factory\AppFactory;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

require __DIR__ . '/includes/application_top.php';

// ----------------------------------------------------------------------------
// INITIALIZE API - SLIM FRAMEWORK
// ----------------------------------------------------------------------------

/**
 * API Version
 *
 * The current API version will be included within every response in the "X-API-Version" header so that
 * clients know which exact version they are using.
 *
 * @var string
 */
$version = '2.6.0';

/**
 * API Environment
 *
 * If the ".dev-environment" file is present it will override the API_V2_ENVIRONMENT value and
 * it will set the environment back to testing ('development' is only suitable for complete error display).
 *
 * @var string
 */
$environment = file_exists(__DIR__ . '/.dev-environment') ? 'test' : 'production';

/**
 * Initialize Slim app
 */

$api = AppFactory::create();
$api->setBasePath(DIR_WS_CATALOG . 'api.php');

switch ($environment) {
    case 'development': // Complete verbose (HTML) output when errors occur.
        $errorMiddleware = $api->addErrorMiddleware(true, false, false);
        break;
    
    case 'test': // Includes PHP errors in the response body (stack trace).
        $errorMiddleware = $api->addErrorMiddleware(true, false, false);
        break;
    
    case 'production': // Will display error info in JSON format but hide extra information.
        $errorMiddleware = $api->addErrorMiddleware(false, false, false);
        break;
    
    default:
        throw new Exception('Invalid APIv2 environment selected: ' . $environment);
}

// ----------------------------------------------------------------------------
// CONTROLLER ROUTING FOR V2
// ----------------------------------------------------------------------------

$api->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'],
          '/v2[/{uri:.*}]',
    function (Request $request, Response $response, array $args) use ($version) {
        $response = $response->withHeader('X-API-Version', 'v' . $version);
    
        $isUriArgAvailable = array_key_exists('uri', $args) && !empty($args['uri']) && $args['uri'] !== '/';
        $uri               = $isUriArgAvailable ? explode('/', $args['uri']) : [];
        $resourceName      = explode('_', ucfirst($uri[0] ?? ''));
        
        foreach ($resourceName as &$section) {
            $section = ucfirst($section);
        }
        unset($section);
        
        $resourceName = implode('', $resourceName);
        
        $controllerName = !empty($uri) ? $resourceName
                                         . 'ApiV2Controller' : HttpApiV2Controller::DEFAULT_CONTROLLER_NAME;
        
        $apiV2Authenticator = MainFactory::create('ApiV2Authenticator', $request, $response, $uri);
        $apiV2Authenticator->authorize($controllerName);
        
        // Check if the resource exists (there is no such method in MainFactory so we use the autoloader function).
        if (!class_exists($controllerName)) {
            throw new HttpApiV2Exception('Resource not found. ' . $controllerName, 404);
        }
        
        $controller = MainFactory::create($controllerName, $request, $response, $uri);
        
        $callable = HttpApiV2Controller::getCallableResource($controller, $uri, $request);
        
        if (!is_callable($callable['resource'])) {
            throw new HttpApiV2Exception('The requested resource is not supported by the API v2.', 405);
        }
        
        if (array_key_exists('params', $callable)) {
            call_user_func($callable['resource'], $callable['params']);
        } else {
            call_user_func($callable['resource']);
        }
        
        return $controller->getResponse();
    });

// ----------------------------------------------------------------------------
// API ERROR HANDLING
// ----------------------------------------------------------------------------

$errorMiddleware->setDefaultErrorHandler(function (
    Request   $request,
    Throwable $ex,
              $displayErrorDetails,
              $logErrors,
              $logErrorDetails
) use ($version, $environment, $api) {
    $response = $api->getResponseFactory()->createResponse();
    
    $responseErrorCode = 500; // The default value for exceptions on server.
    if (is_a($ex, 'HttpApiV2Exception')) // An HttpApiException will contain a specific HTTP status code.
    {
        $responseErrorCode = $ex->getCode();
    }
    
    // For security reasons we should add the X-API-Version header only for authorized requests
    if ($responseErrorCode === 401) {
        $response = $response->withHeader('WWW-Authenticate', 'Basic realm="Gambio GX3 APIv2 Login"');
    } else {
        $response = $response->withHeader('X-API-Version', 'v' . $version);
    }
    
    $response = $response->withStatus($responseErrorCode);
    $response = $response->withHeader('Content-Type', 'application/json');
    
    $responseContent = [
        'code'    => $ex->getCode(),
        'status'  => 'error',
        'message' => $ex->getMessage(),
        'request' => [
            'method' => $request->getMethod(),
            'url'    => $request->getUri()->getHost(),
            'path'   => $request->getUri()->getPath(),
            'uri'    => [
                'root'     => GM_HTTP_SERVER . DIR_WS_CATALOG . 'api.php/v2/',
                'resource' => $request->getRequestTarget(),
            ],
        ],
    ];
    
    // Provide error stack only in 'test' mode.
    if ($environment === 'test' && $displayErrorDetails) {
        $responseContent['error'] = [
            'file'  => $ex->getFile(),
            'line'  => $ex->getLine(),
            'stack' => $ex->getTrace(),
        ];
    }
    
    if (defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_SLASHES')) {
        $response->getBody()->write(json_encode($responseContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    } else {
        $response->getBody()->write(json_encode($responseContent)); // PHP v5.3
    }
    
    return setRateLimitHeader($request, $response);
});

// ----------------------------------------------------------------------------
// SET RATE LIMIT HEADER FOR ERROR HANDLING
// ----------------------------------------------------------------------------

function setRateLimitHeader(Request $request, Response $response)
{
    // Load or create cache file.
    $cacheFilePath = DIR_FS_CATALOG . 'cache/gxapi_v2_sessions_' . FileLog::get_secure_token();
    if (!file_exists($cacheFilePath)) {
        touch($cacheFilePath);
        $sessions = [];
    } else {
        $sessions = unserialize(file_get_contents($cacheFilePath));
    }
    
    // Clear expired sessions.
    foreach ($sessions as $index => $session) {
        if ($session['reset'] < time()) {
            unset($sessions[$index]);
        }
    }
    
    // Get session identifier from request.
    $identifier = md5($request->getHeader('Authorization')[0] ?? '');
    if (empty($identifier)) {
        throw new HttpApiV2Exception('Remote address value was not provided.', 400);
    }
    
    // Check session entry, if not found create one.
    if (!isset($sessions[$identifier])) {
        $sessions[$identifier] = [
            'limit'     => AbstractApiV2Controller::DEFAULT_RATE_LIMIT,
            'remaining' => AbstractApiV2Controller::DEFAULT_RATE_LIMIT,
            'reset'     => time() + (AbstractApiV2Controller::DEFAULT_RATE_RESET_PERIOD * 60),
        ];
    } else {
        if ($sessions[$identifier]['remaining'] <= 0) {
            throw new HttpApiV2Exception('Request limit was reached.', 429);
        }
    }
    
    // Set rate limiting headers to response.
    $sessions[$identifier]['remaining']--;
    $response = $response->withHeader('X-Rate-Limit-Limit', $sessions[$identifier]['limit']);
    $response = $response->withHeader('X-Rate-Limit-Remaining', $sessions[$identifier]['remaining']);
    $response = $response->withHeader('X-Rate-Limit-Reset', $sessions[$identifier]['reset']);
    
    file_put_contents($cacheFilePath, serialize($sessions));
    
    return $response;
}

// ----------------------------------------------------------------------------
// API EXECUTION
// ----------------------------------------------------------------------------

$api->run();
