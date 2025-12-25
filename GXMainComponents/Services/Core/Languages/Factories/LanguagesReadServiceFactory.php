<?php
/*--------------------------------------------------------------
   LanguagesReadServiceFactory.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class LanguagesReadServiceFactory
 */
class LanguagesReadServiceFactory
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var LanguagesReadService
     */
    protected $service;
    
    
    /**
     * LanguagesReadServiceFactory constructor.
     *
     * @param CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * @return LanguagesReadService
     */
    public function service(): LanguagesReadService
    {
        if ($this->service === null) {
            
            $this->service = new LanguagesReadService($this->repository());
        }
        
        return $this->service;
    }
    
    
    /**
     * @return LanguagesReadRepository
     */
    protected function repository(): LanguagesReadRepository
    {
        $reader = new LanguageReader($this->queryBuilder);
        
        return new LanguagesReadRepository($reader, new LanguageDTOMapper);
    }
}