<?php
/* --------------------------------------------------------------
  ThemeLanguageProvider.php 2019-08-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class LanguageService
 */
class LanguageService
{
    /**
     * @var LanguageTextManager[]
     */
    protected $textManagers = [];
    
    /**
     * @var string
     */
    protected $languageId;
    
    /**
     * LanguageService constructor.
     * @param string $languageId
     */
    public function __construct(string $languageId)
    {
        $this->languageId = $languageId;
    }
    
    /**
     * @param string $section
     * @return LanguageTextManager
     */
    protected function textManagerForSection(string $section): LanguageTextManager
    {
        if (!array_key_exists($section, $this->textManagers)) {
            
            $this->textManagers[$section] = MainFactory::create(LanguageTextManager::class, $section, $this->languageId);
        }
        
        return $this->textManagers[$section];
    }
    
    /**
     * @param string $content
     * @return string
     */
    public function translate(string $content): string
    {
        if (strpos($content, '.') === false) {
            
            return $content;
        }
        
        [$section, $phrase] = explode('.', $content, 2);
        
        $textManager = $this->textManagerForSection($section);
        
        return $textManager->get_text($phrase);
    }
}