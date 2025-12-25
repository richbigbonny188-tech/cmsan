<?php
/* --------------------------------------------------------------
   SingleSignonModuleCenterModule.inc.php 2023-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SingleSignonModuleCenterModule extends AbstractModuleCenterModule
{
    public const COOKIE_CONSENT_SSO_ALIAS = 'gambio/singleSignOn';
    
    
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('singlesignon_title');
        $this->description = $this->languageTextManager->get_text('singlesignon_description');
        $this->sortOrder   = 59454;
    }
    
    
    /**
     * Installs the module
     *
     * @return void
     */
    public function install(): void
    {
        $query = 'CREATE TABLE IF NOT EXISTS `customers_sso` (
  				`customers_sso_id` int(11) NOT NULL AUTO_INCREMENT,
  				`customers_id` int(11) NOT NULL,
  				`issuer` varchar(255) NOT NULL,
				`subject` varchar(255) NOT NULL,
  				PRIMARY KEY (`customers_sso_id`),
  				UNIQUE KEY `issuer_subject` (`issuer`,`subject`),
  				KEY `customers_id` (`customers_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $this->db->query($query);
        
        $this->installCookieConsentPurposes();
        
        parent::install();
    }
    
    
    /**
     * Uninstalls the module
     *
     * @return void
     */
    public function uninstall(): void
    {
        $this->uninstallCookieConsentPurposes();
        
        parent::uninstall();
    }
    
    
    /**
     * Inserts into the database the SSO cookie consent purpose
     *
     * @return void
     */
    protected function installCookieConsentPurposes(): void
    {
        $purposeAlias = self::COOKIE_CONSENT_SSO_ALIAS;
        $result       = $this->db->select('*')
            ->from('cookie_consent_panel_purposes')
            ->where("purpose_alias = '{$purposeAlias}'")
            ->get()
            ->result_array();
        
        // If the purpose is already in the database, return
        if (count($result)) {
            return;
        }
        
        $lastId = $this->lastInsertId();
        $query  = "INSERT INTO `cookie_consent_panel_purposes` (
                    `purpose_id`,
                    `language_id`,
                    `category_id`,
                    `purpose_name`,
                    `purpose_description`,
                    `purpose_alias`,
                    `purpose_status`,
                    `purpose_deletable`
                ) VALUES
                ({$lastId}, 1, 2, 'SingleSignOn', '', '{$purposeAlias}', 0, 0),
                ({$lastId}, 2, 2, 'SingleSignOn', '', '{$purposeAlias}', 0, 0);";
        
        $this->db->query($query);
    }
    
    
    /**
     * Deletes from the database the SSO cookie consent purpose
     *
     * @return void
     */
    protected function uninstallCookieConsentPurposes(): void
    {
        $purposeAlias = self::COOKIE_CONSENT_SSO_ALIAS;
        
        $this->db->delete('cookie_consent_panel_purposes', "purpose_alias = '{$purposeAlias}'");
    }
    
    
    /**
     * Gets the `cookie_consent_panel_purposes` next ID
     *
     * @return int
     */
    protected function lastInsertId(): int
    {
        $maxPurposeId = $this->db->select('MAX(`purpose_id`) as max')
            ->from('cookie_consent_panel_purposes')
            ->get()
            ->result_array();
        $maxPurposeId = array_shift($maxPurposeId)['max'];
        $maxPurposeId = $maxPurposeId ?? 0;
        
        return ++$maxPurposeId;
    }
}
