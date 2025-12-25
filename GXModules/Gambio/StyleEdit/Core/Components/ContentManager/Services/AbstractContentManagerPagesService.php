<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPagesService.php 2021-07-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services;

use ContentDeleterInterface;
use ContentReadServiceInterface;
use ContentValueObjectFactory;
use ContentWriteServiceInterface;
use GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Interfaces\ContentManagerPagesServiceInterface;
use PagePosition;

/**
 * Class ContentManagerPagesService
 *
 * @package GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services
 */
abstract class AbstractContentManagerPagesService implements ContentManagerPagesServiceInterface
{
    /**
     * @var ContentWriteServiceInterface
     */
    protected $contentWriteService;
    
    /**
     * @var ContentReadServiceInterface
     */
    protected $contentReadService;
    
    /**
     * @var ContentDeleterInterface
     */
    protected $contentDeleteService;
    
    /**
     * @var ContentValueObjectFactory
     */
    protected $contentValueObjectFactory;
    
    
    /**
     * ContentManagerPagesService constructor.
     *
     * @param ContentWriteServiceInterface $contentWriteService
     * @param ContentReadServiceInterface  $contentReadService
     * @param ContentDeleterInterface      $contentDeleteService
     * @param ContentValueObjectFactory    $contentValueObjectFactory
     */
    public function __construct(
        ContentWriteServiceInterface $contentWriteService,
        ContentReadServiceInterface $contentReadService,
        ContentDeleterInterface $contentDeleteService,
        ContentValueObjectFactory $contentValueObjectFactory
    ) {
        
        $this->contentWriteService       = $contentWriteService;
        $this->contentReadService        = $contentReadService;
        $this->contentDeleteService      = $contentDeleteService;
        $this->contentValueObjectFactory = $contentValueObjectFactory;
    }
    
    
    /**
     * @return ContentWriteServiceInterface
     */
    public function writeService(): ContentWriteServiceInterface
    {
        return $this->contentWriteService;
    }
    
    
    /**
     * @return ContentReadServiceInterface
     */
    public function readService(): ContentReadServiceInterface
    {
        return $this->contentReadService;
    }
    
    
    /**
     * @return ContentDeleterInterface
     */
    public function deleter(): ContentDeleterInterface
    {
        return $this->contentDeleteService;
    }
    
    
    /**
     * @return ContentValueObjectFactory
     */
    public function contentValueObjectFactory(): ContentValueObjectFactory
    {
        return $this->contentValueObjectFactory;
    }
    
    
    /**
     * @param string $position
     *
     * @return PagePosition
     */
    public function createPagePositionFromString(string $position): PagePosition
    {
        return PagePosition::createFromString($position);
    }
    
    
    public function findPageById(\ContentIdentificationInterface $identification): \ContentInterface
    {
        return $this->contentReadService->findById($identification);
    }
    
    
    /**
     * @param $contentManagerContent
     */
    abstract public function createPage($contentManagerContent): void;
    
    
    /**
     * @param $contentManagerContent
     */
    abstract public function updatePage($contentManagerContent): void;
    
    
    /**
     * @param \ContentIdentificationInterface $identification
     */
    abstract public function deletePage(\ContentIdentificationInterface $identification): void;
}