<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerLinkPageService.php 2021-07-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services;

/**
 * Class ContentManagerLinkPageService
 *
 * @package GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services
 */
class ContentManagerLinkTypeService extends AbstractContentManagerPagesService
{
    
    /**
     * @param $contentManagerContent
     */
    public function createPage($contentManagerContent): void
    {
        $this->contentWriteService->storeLinkPageContent($contentManagerContent);
    }
    
    
    /**
     * @param $contentManagerContent
     */
    public function updatePage($contentManagerContent): void
    {
        $this->contentWriteService->updateLinkPageContent($contentManagerContent);
    }
    
    
    /**
     * @param \ContentIdentificationInterface $identification
     */
    public function deletePage(\ContentIdentificationInterface $identification): void
    {
        $this->contentDeleteService->deleteById($identification);
    }
}