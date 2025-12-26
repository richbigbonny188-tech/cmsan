<?php

/* --------------------------------------------------------------
   StaticSeoUrlReadService.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StaticSeoUrlReadService
 *
 * @category System
 * @package  StaticSeoUrls
 */
class StaticSeoUrlReadService implements StaticSeoUrlReadServiceInterface
{
    /**
     * @var StaticSeoUrlRepositoryInterface
     */
    protected $staticSeoUrlRepository;
    
    
    /**
     * StaticSeoUrlReadService constructor.
     *
     * @param StaticSeoUrlRepositoryInterface $staticSeoUrlRepository
     */
    public function __construct(StaticSeoUrlRepositoryInterface $staticSeoUrlRepository)
    {
        $this->staticSeoUrlRepository = $staticSeoUrlRepository;
    }
    
    
    /**
     * Returns a StaticSeoUrlCollection with all existing StaticSeoUrl objects.
     *
     * @return StaticSeoUrlCollection
     */
    public function getAllStaticSeoUrls()
    {
        return $this->staticSeoUrlRepository->getAll();
    }
    
    
    /**
     * Returns a StaticSeoUrl instance by the given staticSeoUrl ID.
     *
     * @param IdType $staticSeoUrlId
     *
     * @return StaticSeoUrlInterface
     */
    public function getStaticSeoUrlById(IdType $staticSeoUrlId)
    {
        return $this->staticSeoUrlRepository->getById($staticSeoUrlId);
    }
}