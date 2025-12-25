<?php
/*--------------------------------------------------------------------------------------------------
    LanguageServiceInterface.php 2019-10-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */
namespace Gambio\StyleEdit\Adapters\Interfaces;

interface LanguageServiceAdapterInterface
{
    /**
     * @param string $content
     *
     * @return string
     */
    public function translate(string $content): string;
    
    
    /**
     * mus return an array with LanguageId=>LanguageInitials like [1 =>'en']
     * @return array
     */
    public function languages(): array;
    
    
    /**
     * @param string $languageCode
     *
     * @return int
     */
    public function getIdLanguageByCode(string $languageCode): int ;
    
}