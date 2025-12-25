<?php

/* --------------------------------------------------------------
   StaticSeoUrlWriteService.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlWriteService
 *
 * @category System
 * @package  StaticSeoUrl
 */
class StaticSeoUrlWriteService implements StaticSeoUrlWriteServiceInterface
{
    /**
     * @var StaticSeoUrlRepositoryInterface
     */
    protected $staticSeoUrlRepository;
    
    
    /**
     * StaticSeoUrlWriteService constructor.
     *
     * @param StaticSeoUrlRepositoryInterface $staticSeoUrlRepository
     */
    public function __construct(StaticSeoUrlRepositoryInterface $staticSeoUrlRepository)
    {
        $this->staticSeoUrlRepository = $staticSeoUrlRepository;
    }
    
    
    /**
     * Deletes a StaticSeoUrl by the given static seo url ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlWriteServiceInterface Same instance for method chaining.
     */
    public function deleteStaticSeoUrlById(IdType $staticSeoUrlId)
    {
        $this->staticSeoUrlRepository->deleteStaticSeoUrlById($staticSeoUrlId);
        
        return $this;
    }
    
    
    /**
     * Saves a static seo url to the database and delegates to child-repositories.
     *
     * @param StaticSeoUrlInterface $staticSeoUrl
     *
     * @return StaticSeoUrlInterface The StaticSeoUrl instance.
     */
    public function saveStaticSeoUrl(StaticSeoUrlInterface $staticSeoUrl)
    {
        $this->staticSeoUrlRepository->store($staticSeoUrl);
        
        return $staticSeoUrl;
    }
}