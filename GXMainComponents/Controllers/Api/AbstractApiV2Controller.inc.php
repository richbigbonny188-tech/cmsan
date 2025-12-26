<?php

/* --------------------------------------------------------------
   AbstractApiV2Controller.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

/**
 * Class AbstractApiV2Controller
 *
 * This class defines the inner core functionality of a ApiV2Controller. It contains the
 * initialization and request validation functionality that every controller must have.
 *
 * The functionality of this class is mark as private because child controllers must not alter
 * the state at this point but rather adjust to it. This will force them to follow the same
 * principles and methodologies.
 *
 * Child API controllers can use the "init" method to initialize their common dependencies.
 *
 * @category System
 * @package  ApiV2Controllers
 */
abstract class AbstractApiV2Controller
{
    /**
     * Defines the default page offset for responses that return multiple items.
     *
     * @var int
     */
    const DEFAULT_PAGE_ITEMS = 50;
    
    /**
     * Default controller to be loaded when no resource was selected.
     *
     * @var string
     */
    const DEFAULT_CONTROLLER_NAME = 'DefaultApiV2Controller';
    
    /**
     * Defines the maximum request limit for an authorized client.
     *
     * @var int
     */
    const DEFAULT_RATE_LIMIT = 5000;
    
    /**
     * Defines the duration of an API session in minutes.
     *
     * @var int
     */
    const DEFAULT_RATE_RESET_PERIOD = 15;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var Response
     */
    protected $response;
    
    /**
     * Contains the request URI segments after the root api version segment.
     *
     * Example:
     *    URI  - api.php/v2/customers/73/addresses
     *    CODE - $this->uri[1]; // will return '73'
     *
     * @var array
     */
    protected $uri;
    
    
    /**
     * AbstractApiV2Controller Constructor
     *
     * Call this constructor from every child controller class in order to set the
     * Slim instance and the request routes arguments to the class.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $uri This array contains all the segments of the current request, starting from the resource.
     *
     * @throws HttpApiV2Exception Through _validateRequest
     * @deprecated The "__initialize" method will is deprecated and will be removed in a future version. Please use
     *             the new "init" for bootstrapping your child API controllers.
     *
     */
    public function __construct(Request $request, Response $response, array $uri)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->uri      = $uri;
        
        if (method_exists($this, '__initialize')) // Method for child-controller initialization stuff (deprecated).
        {
            $this->__initialize();
        }
        
        if (method_exists($this, 'init')) // Method for child-controller initialization stuff (new method).
        {
            $this->init();
        }
        
        $this->_validateRequest();
        $this->_prepareResponse();
    }
    
    
    /**
     * Validate request before proceeding with response.
     *
     * This method will validate the request headers, user authentication and other parameters
     * before the controller proceeds with the response.
     *
     * @throws HttpApiV2Exception If validation fails - 415 Unsupported media type.
     */
    protected function _validateRequest()
    {
        $requestMethod = $this->request->getMethod();
        $contentType   = $this->request->getHeader('Content-Type')[0] ?? '';
        
        if (($requestMethod === 'POST' || $requestMethod === 'PUT' || $requestMethod === 'PATCH')
            && empty($_FILES)
            && $contentType !== 'application/json') {
            throw new HttpApiV2Exception('Unsupported Media Type HTTP', 415);
        }
        
        $this->_setRateLimitHeader();
    }
    
    
    /**
     * Prepare response headers.
     *
     * This method will prepare default attributes of the API responses. Further response
     * settings must be set explicitly from each controller method separately.
     */
    protected function _prepareResponse()
    {
        $shopVersion = gm_get_conf('INSTALLED_VERSION');
        $shopVersion = strpos($shopVersion, 'v') === 0 ? $shopVersion : 'v' . $shopVersion;
        
        $this->response = $this->response->withStatus(200);
        $this->response = $this->response->withHeader('Content-Type', 'application/json; charset=utf-8');
        $this->response = $this->response->withHeader('X-Shop-Version', $shopVersion);
    }
    
    
    /**
     * Handle rate limit headers.
     *
     * There is a cache file that will store each user session and provide a security
     * mechanism that will protect the shop from DOS attacks or service overuse. Each
     * session will use the hashed "Authorization header" to identify the client. When
     * the limit is reached a "HTTP/1.1 429 Too Many Requests" will be returned.
     *
     * Headers:
     *   X-Rate-Limit-Limit     >> Max number of requests allowed.
     *   X-Rate-Limit-Remaining >> Number of requests remaining.
     *   X-Rate-Limit-Reset     >> UTC epoch seconds until the limit is reset.
     *
     * Important: This method will be executed in every API call and it might slow the
     * response time due to filesystem operations. If the difference is significant
     * then it should be optimized.
     *
     * @throws HttpApiV2Exception If request limit exceed - 429 Too Many Requests
     */
    protected function _setRateLimitHeader()
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
        $identifier = md5($this->request->getHeader('Authorization')[0]);
        if (empty($identifier)) {
            throw new HttpApiV2Exception('Remote address value was not provided.', 400);
        }
        
        // Check session entry, if not found create one.
        if (!isset($sessions[$identifier])) {
            $sessions[$identifier] = [
                'limit'     => self::DEFAULT_RATE_LIMIT,
                'remaining' => self::DEFAULT_RATE_LIMIT,
                'reset'     => time() + (self::DEFAULT_RATE_RESET_PERIOD * 60)
            ];
        } else {
            if ($sessions[$identifier]['remaining'] <= 0) {
                throw new HttpApiV2Exception('Request limit was reached.', 429);
            }
        }
        
        // Set rate limiting headers to response.
        $sessions[$identifier]['remaining']--;
        $this->response = $this->response->withHeader('X-Rate-Limit-Limit', $sessions[$identifier]['limit']);
        $this->response = $this->response->withHeader('X-Rate-Limit-Remaining', $sessions[$identifier]['remaining']);
        $this->response = $this->response->withHeader('X-Rate-Limit-Reset', $sessions[$identifier]['reset']);
        
        file_put_contents($cacheFilePath, serialize($sessions));
    }
    
    
    /**
     * @return string
     */
    protected function getRootUri()
    {
        return GM_HTTP_SERVER . DIR_WS_CATALOG . 'api.php/v2/';
    }
    
    
    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}