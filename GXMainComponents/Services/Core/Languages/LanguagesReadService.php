<?php
/*--------------------------------------------------------------
   LanguagesReadService.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class LanguagesReadService
 */
class LanguagesReadService
{
    /**
     * @var LanguagesReadRepository
     */
    protected $repository;
    
    
    /**
     * LanguagesReadService constructor.
     *
     * @param LanguagesReadRepository $repository
     */
    public function __construct(LanguagesReadRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @return LanguageDTOCollection
     */
    public function getLanguages(): LanguageDTOCollection
    {
        return $this->repository->getLanguages();
    }
}