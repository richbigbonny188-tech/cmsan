<?php
/* --------------------------------------------------------------
   ContentService.inc.php 2019-08-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ContentWriteService
 */
class ContentWriteService implements ContentWriteServiceInterface
{
    /**
     * @var ContentRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ContentService constructor.
     *
     * @param ContentRepositoryInterface $repository
     */
    public function __construct(ContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Stores an info page content.
     *
     * @param InfoPageContent $infoPage
     */
    public function storeInfoPageContent(InfoPageContent $infoPage): void
    {
        $this->repository->storeInfoPageContent($infoPage);
    }
    
    
    /**
     * Stores an link page content.
     *
     * @param LinkPageContent $linkPage
     */
    public function storeLinkPageContent(LinkPageContent $linkPage): void
    {
        $this->repository->storeLinkPageContent($linkPage);
    }
    
    
    /**
     * Stores an info element content.
     *
     * @param InfoElementContent $infoElement
     */
    public function storeInfoElementContent(InfoElementContent $infoElement): void
    {
        $this->repository->storeInfoElementContent($infoElement);
    }
    
    
    /**
     * Stores a collection of info page content.
     *
     * @param \InfoPageContentCollection $infoPageCollection
     */
    public function storeInfoPageContentCollection(InfoPageContentCollection $infoPageCollection)
    {
        if ($infoPageCollection->count()) {
            foreach ($infoPageCollection->getArray() as $infoPage) {
                $this->storeInfoPageContent($infoPage);
            }
        }
    }
    
    
    /**
     * Stores a collection of info element content.
     *
     * @param \InfoElementContentCollection $infoElementCollection
     */
    public function storeInfoElementContentCollection(InfoElementContentCollection $infoElementCollection)
    {
        if ($infoElementCollection->count()) {
            foreach ($infoElementCollection->getArray() as $infoElement) {
                $this->storeInfoElementContent($infoElement);
            }
        }
    }
    
    
    /**
     * Stores a collection of page info content.
     *
     * @param \LinkPageContentCollection $linkPageCollection
     */
    public function storeLinkPageContentCollection(LinkPageContentCollection $linkPageCollection)
    {
        if ($linkPageCollection->count()) {
            foreach ($linkPageCollection->getArray() as $linkPage) {
                $this->storeLinkPageContent($linkPage);
            }
        }
    }
    
    
    /**
     * Updates an info page content.
     *
     * @param InfoPageContent $infoPage
     */
    public function updateInfoPageContent(InfoPageContent $infoPage): void
    {
        $this->repository->updateInfoPageContent($infoPage);
    }
    
    
    /**
     * Updates a link page content.
     *
     * @param LinkPageContent $linkPage
     */
    public function updateLinkPageContent(LinkPageContent $linkPage): void
    {
        $this->repository->updateLinkPageContent($linkPage);
    }
    
    
    /**
     * Updates an info element content
     *
     * @param InfoElementContent $infoElement
     */
    public function updateInfoElementContent(InfoElementContent $infoElement): void
    {
        $this->repository->updateInfoElementContent($infoElement);
    }
}