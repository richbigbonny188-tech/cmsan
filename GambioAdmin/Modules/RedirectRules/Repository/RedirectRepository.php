<?php
/* --------------------------------------------------------------
   RedirectRepository.php 2023-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules\Repository;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\RedirectRules\Entities\RedirectRule;
use Gambio\Admin\Modules\RedirectRules\Exceptions\RedirectRuleException;

/*
 *

CREATE TABLE IF NOT EXISTS `redirectrules` (
  `redirect_id` int(11) NOT NULL AUTO_INCREMENT,
  `url_path` varchar(200) NOT NULL,
  `query` varchar(200) NOT NULL,
  `query_match_mode` varchar(12) NOT NULL DEFAULT 'ignore',
  `response_code` int(11) NOT NULL DEFAULT '302',
  `target` varchar(200) NOT NULL,
  `query_processing` varchar(6) NOT NULL DEFAULT 'merge',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`redirect_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 */

class RedirectRepository implements RedirectRepositoryInterface
{
    protected const DEFAULT_PAGE_SIZE = 20;
    /**
     * @var Connection
     */
    protected $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param int $ruleId
     *
     * @return RedirectRule
     * @throws RedirectRuleException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRedirectRule(int $ruleId): RedirectRule
    {
        $queryBuilder         = $this->connection->createQueryBuilder();
        $dbRedirectsStatement = $queryBuilder->select('*')
            ->from('redirectrules')
            ->where('redirect_id = ' . $queryBuilder->createPositionalParameter($ruleId))
            ->executeQuery();
        $row                  = $dbRedirectsStatement->fetchAssociative();
        if (empty($row)) {
            throw new RedirectRuleException("Rule with id {$ruleId} not found.");
        }
        $redirectRule = new RedirectRule((int)$row['redirect_id'],
                                         $row['url_path'],
                                         $row['query'],
                                         $row['query_match_mode'],
                                         (int)$row['response_code'],
                                         $row['target'],
                                         $row['query_processing'],
                                         (int)$row['status'] === 1);
        
        return $redirectRule;
    }
    
    
    /**
     * @param string $path
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function findRedirectRulesByPath(string $path): array
    {
        $redirectRules        = [];
        $queryBuilder         = $this->connection->createQueryBuilder();
        $dbRedirectsStatement = $queryBuilder->select('*')
            ->from('redirectrules')
            ->where('url_path = :path')
            ->andWhere('status = 1')
            ->setParameter('path', $path)
            ->executeQuery();
        foreach ($dbRedirectsStatement->fetchAllAssociative() as $redirectRow) {
            $redirectRules[] = new RedirectRule((int)$redirectRow['redirect_id'],
                                                $redirectRow['url_path'],
                                                $redirectRow['query'],
                                                $redirectRow['query_match_mode'],
                                                (int)$redirectRow['response_code'],
                                                $redirectRow['target'],
                                                $redirectRow['query_processing'],
                                                (int)$redirectRow['status'] === 1);
        }
        
        return $redirectRules;
    }
    
    
    /**
     * @param string $path
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function findPrefixRulesByPath(string $path): array
    {
        $redirectRules        = [];
        $queryBuilder         = $this->connection->createQueryBuilder();
        $dbRedirectsStatement = $queryBuilder->select('*')
            ->from('redirectrules')
            ->where(':path LIKE REPLACE(`url_path`, \'*\', \'%\')')
            ->andWhere("`url_path` != '*'")
            ->andWhere('status = 1')
            ->orderBy('LENGTH(`url_path`)', 'DESC')
            ->setParameter('path', $path)
            ->executeQuery();
        foreach ($dbRedirectsStatement->fetchAllAssociative() as $redirectRow) {
            $urlPath               = str_replace('*', '', $redirectRow['url_path']);
            $pathSuffix            = str_replace($urlPath, '', $path);
            $targetPrefix          = str_replace('*', '', $redirectRow['target']);
            $redirectRow['target'] = $targetPrefix . $pathSuffix;
            
            $redirectRules[] = new RedirectRule((int)$redirectRow['redirect_id'],
                                                $redirectRow['url_path'],
                                                $redirectRow['query'],
                                                $redirectRow['query_match_mode'],
                                                (int)$redirectRow['response_code'],
                                                $redirectRow['target'],
                                                $redirectRow['query_processing'],
                                                (int)$redirectRow['status'] === 1);
        }
        
        return $redirectRules;
    }
    
    
    /**
     * @param string $query
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function findRedirectRulesByQuery(string $query): array
    {
        $redirectRules        = [];
        $queryBuilder         = $this->connection->createQueryBuilder();
        $dbRedirectsStatement = $queryBuilder->select('*')
            ->from('redirectrules')
            ->where('url_path = \'*\'')
            ->andWhere('query = ' . $queryBuilder->createNamedParameter($query))
            ->andWhere('status = 1')
            ->executeQuery();
        foreach ($dbRedirectsStatement->fetchAllAssociative() as $redirectRow) {
            $redirectRules[] = new RedirectRule((int)$redirectRow['redirect_id'],
                                                $redirectRow['url_path'],
                                                $redirectRow['query'],
                                                $redirectRow['query_match_mode'],
                                                (int)$redirectRow['response_code'],
                                                $redirectRow['target'],
                                                $redirectRow['query_processing'],
                                                (int)$redirectRow['status'] === 1);
        }
        
        return $redirectRules;
    }
    
    
    /**
     * @param int $pageSize
     *
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNumberOfPages(int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        $queryBuilder  = $this->connection->createQueryBuilder();
        $numRowsResult = $queryBuilder->select('COUNT(*) AS num_rows')
            ->from('redirectrules')
            ->executeQuery()
            ->fetchAssociative();
        
        return (int)ceil($numRowsResult['num_rows'] / $pageSize);
    }
    
    
    /**
     * @param int $pageNumber
     * @param int $pageSize
     *
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllRedirectRules(int $pageNumber = 0, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        $redirectRules = [];
        $queryBuilder  = $this->connection->createQueryBuilder();
        if ($pageNumber > 0) {
            $dbRedirectsStatement = $queryBuilder->select('*')
                ->from('redirectrules')
                ->setMaxResults($pageSize)
                ->setFirstResult(($pageNumber - 1) * $pageSize)
                ->executeQuery();
        } else {
            $dbRedirectsStatement = $queryBuilder->select('*')->from('redirectrules')->executeQuery();
        }
        
        foreach ($dbRedirectsStatement->fetchAllAssociative() as $redirectRow) {
            $redirectRules[] = new RedirectRule((int)$redirectRow['redirect_id'],
                                                $redirectRow['url_path'],
                                                $redirectRow['query'],
                                                $redirectRow['query_match_mode'],
                                                (int)$redirectRow['response_code'],
                                                $redirectRow['target'],
                                                $redirectRow['query_processing'],
                                                (int)$redirectRow['status'] === 1);
        }
        
        return $redirectRules;
    }
    
    
    /**
     * @param RedirectRule $redirectRule
     *
     * @return RedirectRule
     * @throws \Doctrine\DBAL\Exception
     */
    public function addRedirectRule(RedirectRule $redirectRule): RedirectRule
    {
        $queryBuilder      = $this->connection->createQueryBuilder();
        $dbInsertStatement = $queryBuilder->insert('redirectrules')
            ->values([
                         'url_path' => ':url_path',
                         'query' => ':query',
                         'query_match_mode' => ':query_match_mode',
                         'response_code' => ':response_code',
                         'target' => ':target',
                         'query_processing' => ':query_processing',
                         'status' => ':status',
                     ])
            ->setParameter('url_path', $redirectRule->getUrlPath())
            ->setParameter('query', $redirectRule->getQuery())
            ->setParameter('query_match_mode', $redirectRule->getQueryMatchMode())
            ->setParameter('response_code', $redirectRule->getResponseCode())
            ->setParameter('target', $redirectRule->getTarget())
            ->setParameter('query_processing', $redirectRule->getQueryProcessing())
            ->setParameter('status', ($redirectRule->isStatus() ? 1 : 0));
        $dbResult          = $dbInsertStatement->executeQuery();
        $insertedId        = $this->connection->lastInsertId();
        $insertedRule      = new RedirectRule((int)$insertedId,
                                              $redirectRule->getUrlPath(),
                                              $redirectRule->getQuery(),
                                              $redirectRule->getQueryMatchMode(),
                                              $redirectRule->getResponseCode(),
                                              $redirectRule->getTarget(),
                                              $redirectRule->getQueryProcessing(),
                                              $redirectRule->isStatus());
        
        return $insertedRule;
    }
    
    
    /**
     * @param int $ruleId
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteRedirectRule(int $ruleId): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete('redirectrules')->where('redirect_id = ?')->setParameter(0, $ruleId)->executeQuery();
    }
    
    
    /**
     * @param RedirectRule $redirectRule
     *
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateRedirectRule(RedirectRule $redirectRule): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update('redirectrules')
            ->where('redirect_id = :redirect_id')
            ->setParameter('redirect_id', $redirectRule->getId())
            ->set('url_path', ':url_path')
            ->set('query', ':query')
            ->set('query_match_mode', ':query_match_mode')
            ->set('response_code', ':response_code')
            ->set('target', ':target')
            ->set('query_processing', ':query_processing')
            ->set('status', ':status')
            ->setParameter('url_path', $redirectRule->getUrlPath())
            ->setParameter('query', $redirectRule->getQuery())
            ->setParameter('query_match_mode', $redirectRule->getQueryMatchMode())
            ->setParameter('response_code', $redirectRule->getResponseCode())
            ->setParameter('target', $redirectRule->getTarget())
            ->setParameter('query_processing', $redirectRule->getQueryProcessing())
            ->setParameter('status', $redirectRule->isStatus() ? 1 : 0)
            ->executeQuery();
    }
}
