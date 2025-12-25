<?php
/*--------------------------------------------------------------
   LanguageDTOMapper.php 2021-06-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

/**
 * Class LanguageDTOMapper
 */
class LanguageDTOMapper
{
    /**
     * @param mixed ...$data
     *
     * @return LanguageDTOCollection
     */
    public function mapLanguages(array ...$data): LanguageDTOCollection
    {
        $dtos = array_map([$this, 'mapLanguage'], $data);
        
        return new LanguageDTOCollection(...$dtos);
    }
    
    /**
     * @param array $data
     *
     * @return LanguageDTO
     */
    public function mapLanguage(array $data): LanguageDTO
    {
        [
            'languages_id'      => $languagesId,
            'name'              => $name,
            'code'              => $code,
            'image'             => $image,
            'directory'         => $directory,
            'sort_order'        => $sortOrder,
            'status'            => $status,
            'status_admin'      => $statusAdmin,
            'language_currency' => $currency,
            'currencies_id'     => $currencyId
        ] = $data;
    
        return new LanguageDTO((int)$languagesId,
                               $name,
                               $code,
                               $image,
                               $directory,
                               (int)$sortOrder,
                               (bool)(int)$status,
                               (bool)(int)$statusAdmin,
                               $currency,
                               (int)$currencyId);
    }
}