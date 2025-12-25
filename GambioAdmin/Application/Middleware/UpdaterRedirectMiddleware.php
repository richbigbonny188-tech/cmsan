<?php
/* --------------------------------------------------------------
 UpdaterRedirectMiddleware.php 2023-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Middleware;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Throwable;
use Webmozart\Assert\Assert;

/**
 * Class UpdaterRedirectMiddleware
 *
 * @package Gambio\Admin\Application\Middleware
 */
class UpdaterRedirectMiddleware implements MiddlewareInterface
{
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * UpdaterRedirectMiddleware constructor.
     *
     * @param Connection $connection
     * @param Url        $url
     * @param Path       $path
     */
    public function __construct(Connection $connection, Url $url, Path $path)
    {
        $this->connection = $connection;
        $this->url        = $url;
        $this->path       = $path;
    }
    
    
    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // @todo refs #69855: version matching should only be triggered if update is needed (cache/update_needed.flag)
        if ($this->isVersionNotMatching() && $this->isAdmin()) {
            $redirectUrl     = "{$this->url->base()}/gambio_updater";
            $responseFactory = (new DecoratedResponseFactory(new ResponseFactory(), new StreamFactory()));
            $response        = $responseFactory->createResponse(302);
            
            return $response->withRedirect($redirectUrl);
        }
        
        return $handler->handle($request);
    }
    
    
    /**
     * Checks if the currently logged in user is an admin.
     * The method determines this by checking session values.
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        try {
            Assert::keyExists($_SESSION, 'customers_status');
            Assert::keyExists($_SESSION['customers_status'], 'customers_status_id');
            
            return $_SESSION['customers_status']['customers_status_id'] === '0';
        } catch (Exception $e) {
            return false;
        }
    }
    
    
    /**
     * Compares the db and release info version and returns true if they are NOT matching.
     *
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    private function isVersionNotMatching(): bool
    {
        return $this->getDbVersion() !== $this->getReleaseInfoVersion();
    }
    
    
    /**
     * Loads the shop version information from the database.
     *
     * @return string
     * @throws \Doctrine\DBAL\Exception
     */
    private function getDbVersion(): string
    {
        $key = 'gm_configuration/INSTALLED_VERSION';
        $qb  = $this->connection->createQueryBuilder();
        
        $where = "{$this->connection->quoteIdentifier('key')} = {$qb->createNamedParameter($key)}";
        $qb->select($this->connection->quoteIdentifier('value'))->from('gx_configurations')->where($where);
        
        $result = $qb->executeQuery()->fetchAssociative();
        
        return $result['value'] ?? 'db-version-not-found';
    }
    
    
    /**
     * Loads the shop version information from the release_info.php file.
     *
     * @return string
     */
    private function getReleaseInfoVersion(): string
    {
        $notFound    = 'release-info-version-not-found';
        $releaseInfo = "{$this->path->base()}/release_info.php";
        if (!file_exists($releaseInfo)) {
            return $notFound;
        }
        
        try {
            $gx_version = null;
            
            /** @noinspection PhpIncludeInspection */
            include $releaseInfo;
            if ($gx_version) {
                return $gx_version;
            }
        } catch (Throwable $throwable) {
        }
        
        return $notFound;
    }
}