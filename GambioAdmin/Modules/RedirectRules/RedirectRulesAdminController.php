<?php
/* --------------------------------------------------------------
   RedirectRulesAdminController.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules;

use Gambio\Admin\Application\Http\Controller\JSEngineController;
use Gambio\Admin\Modules\RedirectRules\Exceptions\RedirectRuleSyntaxErrorException;
use Gambio\Admin\Modules\RedirectRules\Factories\RedirectServiceFactory;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TemplateEngine\Exceptions\RenderingFailedException;

/**
 * Class RedirectRulesAdminController
 *
 * @package Gambio\Admin\Modules\RedirectRules
 */
class RedirectRulesAdminController extends JSEngineController
{
    /**
     * @var RedirectServiceInterface
     */
    protected $service;
    
    /**
     * @var ConfigurationService
     */
    protected $configurationService;
    
    
    /**
     * RedirectRulesAdminController constructor.
     *
     * @param RedirectServiceFactory $serviceFactory
     * @param ConfigurationService   $configurationService
     */
    public function __construct(RedirectServiceFactory $serviceFactory, ConfigurationService $configurationService)
    {
        $this->service              = $serviceFactory->service();
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws RenderingFailedException
     */
    public function showOverview(Request $request, Response $response): Response
    {
        $this->addJsSectionTranslation('redirectrules');
        $template = $this->render($this->translate('module_title', 'redirectrules'),
                                  __DIR__ . '/ui/configuration.html');
        
        return $response->write($template);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function getConfig(Request $request, Response $response): Response
    {
        $maxAge = 3600;
        if ($this->configurationService->has('redirect/maxAge')) {
            $maxAge = (int)$this->configurationService->find('redirect/maxAge')->value();
        }
        
        return $response->withJson(['maxAge' => $maxAge,]);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function setConfig(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        
        if (isset($data['maxAge'])) {
            $this->configurationService->save('redirect/maxAge', (string)(int)$data['maxAge']);
        }
        
        return $response->withJson(['result' => 'OK',]);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function getRules(Request $request, Response $response): Response
    {
        $rules = $this->service->getRedirectRules();
        
        return $response->withJson(['rules' => $rules->toArray()]);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function addRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        $newRuleData = $data['newRule'];
        if (empty($newRuleData['query']) && strpos($newRuleData['urlPath'], '?') !== false) {
            [$urlPath, $query] = explode('?', $newRuleData['urlPath'], 2);
        } else {
            $urlPath = $newRuleData['urlPath'];
            $query   = $newRuleData['query'];
        }
        $status = isset($newRuleData['status']) ? $newRuleData['status'] === 'true' : true;
        $maxAge = isset($newRuleData['maxAge']) ? (int)$newRuleData['maxAge'] : 3600;
        
        $responseData = [
            'message' => '',
        ];
        
        try {
            $newRule      = $this->service->makeRedirectRule(0,
                                                             $urlPath,
                                                             $query,
                                                             $newRuleData['queryMatchMode'],
                                                             (int)$newRuleData['responseCode'],
                                                             $newRuleData['target'],
                                                             $newRuleData['queryProcessing'],
                                                             $status);
            $insertedRule = $this->service->addRedirectRule($newRule);
        } catch (RedirectRuleSyntaxErrorException $e) {
            $responseData['message'] = $e->getUserMessage() ? : 'syntax_error';
        }
        
        return $response->withJson($responseData);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        $ruleId = (int)$data['ruleId'];
        $this->service->deleteRedirectRule($ruleId);
        
        return $response->withJson(['message' => '']);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function enableRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        $ruleId = (int)$data['ruleId'];
        $this->service->enableRedirectRule($ruleId);
        
        return $response->withJson(['message' => '']);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function disableRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        $ruleId = (int)$data['ruleId'];
        $this->service->disableRedirectRule($ruleId);
        
        return $response->withJson(['message' => '']);
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateRule(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $_SESSION['coo_page_token']->is_valid($data['page_token'] ?? '');
        $ruleData = $data['ruleData'];
        
        if (empty($ruleData['query']) && strpos($ruleData['urlPath'], '?') !== false) {
            [$urlPath, $query] = explode('?', $ruleData['urlPath'], 2);
        } else {
            $urlPath = $ruleData['urlPath'];
            $query   = $ruleData['query'];
        }
        $status = isset($ruleData['status']) ? $ruleData['status'] === 'true' : true;
        $maxAge = isset($ruleData['maxAge']) ? (int)$ruleData['maxAge'] : 3600;
        
        $responseMessage = '';
        try {
            $redirectRule = $this->service->makeRedirectRule((int)$ruleData['id'],
                                                             $urlPath,
                                                             $query,
                                                             $ruleData['queryMatchMode'],
                                                             (int)$ruleData['responseCode'],
                                                             $ruleData['target'],
                                                             $ruleData['queryProcessing'],
                                                             $status);
            $this->service->updateRedirectRule($redirectRule);
        } catch (RedirectRuleSyntaxErrorException $e) {
            $responseMessage = $e->getUserMessage() ? : 'Syntax error in redirect rule: ' . $e->getMessage();
        }
        
        return $response->withJson(['message' => $responseMessage]);
    }
}