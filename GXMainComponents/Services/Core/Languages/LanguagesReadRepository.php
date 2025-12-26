<?php
/*--------------------------------------------------------------
   LanguagesReadRepository.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class LanguagesReadRepository
 */
class LanguagesReadRepository
{
    /**
     * @var LanguageReader
     */
    protected $reader;
    
    /**
     * @var LanguageDTOMapper
     */
    protected $dtoMapper;
    
    
    /**
     * LanguagesReadRepository constructor.
     *
     * @param LanguageReader    $reader
     * @param LanguageDTOMapper $dtoMapper
     */
    public function __construct(
        LanguageReader $reader,
        LanguageDTOMapper $dtoMapper
    ) {
        $this->reader    = $reader;
        $this->dtoMapper = $dtoMapper;
    }
    
    
    /**
     * @return LanguageDTOCollection
     */
    public function getLanguages(): LanguageDTOCollection
    {
        $data = $this->reader->getLanguageData();
        
        return $this->dtoMapper->mapLanguages(...$data);
    }
}