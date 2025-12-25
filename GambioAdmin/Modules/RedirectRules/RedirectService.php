<?php
/* --------------------------------------------------------------
   RedirectService.php 2021-01-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules;

use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRule;
use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRuleCollection;
use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRuleCollectionInterface;
use Gambio\Admin\Modules\RedirectRules\Exceptions\RedirectRuleSyntaxErrorException;
use Gambio\Admin\Modules\RedirectRules\Repository\RedirectRepository;
use Gambio\Admin\Modules\RedirectRules\Repository\RedirectRepositoryInterface;

class RedirectService implements RedirectServiceInterface
{
    /** @var RedirectRepository */
    protected $repository;
    
    
    public function __construct(RedirectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    public function findRedirectByRelativeUri(string $relativeUri): ?RedirectInterface
    {
        $parsed          = parse_url($relativeUri);
        $requestPath     = $parsed['path'] ?? '';
        $requestQueryRaw = $parsed['query'] ?? '';
        parse_str($requestQueryRaw, $requestQueryData);
        ksort($requestQueryData);
        $requestQuery = http_build_query($requestQueryData, '', '&'); // normalized query, i.e. sorted by key
        
        $redirect      = null;
        $matchingRules = $this->repository->findPrefixRulesByPath($requestPath);
        if (empty($matchingRules)) {
            $matchingRules = $this->repository->findRedirectRulesByPath($requestPath);
        }
        if (empty($matchingRules) && !empty($requestQuery)) {
            $matchingRules = $this->repository->findRedirectRulesByQuery($requestQuery);
        }
        foreach ($matchingRules as $rule) {
            if (!($rule->getQueryMatchMode() === 'ignore' || $rule->getQuery() === $requestQuery)) {
                continue;
            }
            
            if ($rule->getQueryProcessing() === 'drop') {
                $redirect = new Redirect($rule->getTarget(), $rule->getResponseCode());
            } else { // queryProcessing === merge
                $targetParts = explode('?', $rule->getTarget(), 2);
                $targetSchemeHostPath = $targetParts[0];
                $targetQuery = $targetParts[1] ?? '';
                parse_str($targetQuery, $targetQueryData);
                $redirectQuery = http_build_query(array_merge($requestQueryData, $targetQueryData), '', '&');
                $redirectUrl   = $targetSchemeHostPath . (!empty($redirectQuery) ? '?' . $redirectQuery : '');
                $redirect      = new Redirect($redirectUrl, $rule->getResponseCode());
            }
            break;
        }
        
        return $redirect;
    }
    
    
    public function getRedirectRules(): RedirectRuleCollectionInterface
    {
        $redirectRulesArray = $this->repository->getAllRedirectRules();
        
        return new RedirectRuleCollection($redirectRulesArray);
    }
    
    
    /**
     * Creates a new RedirectRule
     *
     * @param int    $id
     * @param string $urlPath
     * @param string $query
     * @param string $queryMatchMode
     * @param int    $responseCode
     * @param string $target
     * @param string $queryProcessing
     * @param bool   $status
     *
     * @return RedirectRule
     * @throws RedirectRuleSyntaxErrorException
     */
    public function makeRedirectRule(
        int $id,
        string $urlPath,
        string $query,
        string $queryMatchMode,
        int $responseCode,
        string $target,
        string $queryProcessing,
        bool $status = true
    ): RedirectRule {
        $urlPath = ltrim(trim($urlPath), '/');
        $urlPath = rtrim($urlPath, '?');
        if (empty($urlPath)) {
            $exception = new RedirectRuleSyntaxErrorException('invalid (empty) urlPath');
            $exception->setUserMessage('syntax_error_urlpath_empty');
            throw $exception;
        }
        if (strpos($urlPath, '?') !== false) {
            $exception = new RedirectRuleSyntaxErrorException('query separator (?) in urlPath');
            $exception->setUserMessage('syntax_error_urlpath_query_separator');
            throw $exception;
        }
        $fullUrl = 'https://www.example.org/' . $urlPath;
        if (filter_var($fullUrl, FILTER_VALIDATE_URL) === false) {
            $exception = new RedirectRuleSyntaxErrorException('invalid urlPath');
            $exception->setUserMessage('syntax_error_urlpath_invalid');
            throw $exception;
        }
        if (strpos($urlPath, 'http://') !== false
            || strpos($urlPath, 'https://') !== false) {
            $exception = new RedirectRuleSyntaxErrorException('invalid urlPath, contains scheme');
            $exception->setUserMessage('syntax_error_urlpath_contains_scheme');
            throw $exception;
        }
        if (!(in_array($queryMatchMode, ['match', 'ignore'], true))) {
            throw new RedirectRuleSyntaxErrorException('invalid query match mode');
        }
        if (!in_array($responseCode, $this->getValidResponseCodes(), true)) {
            throw new RedirectRuleSyntaxErrorException('invalid response code');
        }
        parse_str($query, $queryData);
        ksort($queryData);
        $normalizedQuery = http_build_query($queryData, '', '&');
        
        $target = trim($target);
        if (empty($target)) {
            $exception = new RedirectRuleSyntaxErrorException('invalid (empty) target URL');
            $exception->setUserMessage('syntax_error_target_empty');
            throw $exception;
        }
        if (filter_var($target, FILTER_VALIDATE_URL) === false) {
            $exception = new RedirectRuleSyntaxErrorException('invalid target URL');
            $exception->setUserMessage('syntax_error_target_invalid');
            throw $exception;
        }
        
        if ($urlPath === '*') {
            $queryMatchMode = 'match';
            if (empty($normalizedQuery)) {
                $exception = new RedirectRuleSyntaxErrorException('query cannot be empty for query-matching rule');
                $exception->setUserMessage('syntax_error_query_required');
                throw $exception;
            }
        }
        
        return new RedirectRule($id,
                                $urlPath,
                                $normalizedQuery,
                                $queryMatchMode,
                                $responseCode,
                                $target,
                                $queryProcessing,
                                $status);
    }
    
    
    public function getValidResponseCodes(): array
    {
        return array_keys($this->getValidRedirectTypes());
    }
    
    
    public function getValidRedirectTypes(): array
    {
        return [
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
        ];
    }
    
    
    public function addRedirectRule(RedirectRule $redirectRule): RedirectRule
    {
        $redirectRule = $this->repository->addRedirectRule($redirectRule);
        
        return $redirectRule;
    }
    
    
    public function deleteRedirectRule(int $ruleId): void
    {
        $this->repository->deleteRedirectRule($ruleId);
    }
    
    
    public function enableRedirectRule(int $ruleId): void
    {
        $redirectRule = $this->repository->getRedirectRule($ruleId);
        $redirectRule->setStatus(true);
        $this->repository->updateRedirectRule($redirectRule);
    }
    
    
    public function disableRedirectRule(int $ruleId): void
    {
        $redirectRule = $this->repository->getRedirectRule($ruleId);
        $redirectRule->setStatus(false);
        $this->repository->updateRedirectRule($redirectRule);
    }
    
    
    public function updateRedirectRule(RedirectRule $redirectRule): void
    {
        $this->repository->updateRedirectRule($redirectRule);
    }
    
    
}
