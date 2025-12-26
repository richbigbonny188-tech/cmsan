<?php
/*--------------------------------------------------------------------------------------------------
    ContentManagerPageTypeService.php 2021-07-20
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);


namespace GXModules\Gambio\StyleEdit\Core\Components\ContentManager\Services;


class ContentManagerPageTypeService extends AbstractContentManagerPagesService
{

    /**
     * @inheritDoc
     */
    public function createPage($contentManagerContent): void
    {
        $this->contentWriteService->storeInfoPageContent($contentManagerContent);
    }

    /**
     * @inheritDoc
     */
    public function updatePage($contentManagerContent): void
    {
        $this->contentWriteService->updateInfoPageContent($contentManagerContent);
    }

    /**
     * @inheritDoc
     */
    public function deletePage(\ContentIdentificationInterface $identification): void
    {
        $this->contentDeleteService->deleteById($identification);
    }
}