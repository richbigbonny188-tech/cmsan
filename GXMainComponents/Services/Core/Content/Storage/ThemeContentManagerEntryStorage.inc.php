<?php

/* --------------------------------------------------------------
   ThemeContentManagerEntryStorage.inc.php 2019-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ThemeContentManagerEntryStorage
 */
class ThemeContentManagerEntryStorage extends ConfigurationStorage
{
    /**
     * Configuration storage namespace
     */
    protected const CONFIG_STORAGE_NAMESPACE = 'main_components/gambio/content_manager';
    
    /**
     * Secret configuration key name template
     */
    protected const SECRET_CONFIG_KEY_TEMPLATE = 'installed/{THEME_ID}';
    
    
    /**
     * ThemeContentManagerEntryStorage constructor.
     */
    public function __construct()
    {
        parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
    }
    
    
    /**
     * Stores to the database that content manager entries have been created
     *
     * @param StringType $themeId
     *
     * @return \ThemeContentManagerEntryStorage
     * @throws Exception
     */
    public function storeContentManagerEntriesCreatedForTheme(StringType $themeId): self
    {
        $key = $this->configurationKeyForTheme($themeId);
        $this->set($key, '1');
        
        return $this;
    }
    
    
    /**
     * @param StringType $themeId
     *
     * @return bool Where the Entries already created
     * @throws Exception
     */
    public function contentManagerEntriesCreatedForTheme(StringType $themeId): bool
    {
        $key = $this->configurationKeyForTheme($themeId);
        
        return $this->get($key) !== false;
    }
    
    
    /**
     * Return the substituted configuration key string for the provided shop ID
     *
     * @param StringType $themeId Theme ID
     *
     * @return string Substituted secret configuration key
     * @throws Exception
     */
    protected function configurationKeyForTheme(StringType $themeId): string
    {
        if ($themeId->asString() === '') {
            throw new Exception('The themeId was \'\'');
        }
        
        return str_replace('{THEME_ID}', $themeId->asString(), self::SECRET_CONFIG_KEY_TEMPLATE);
    }
}