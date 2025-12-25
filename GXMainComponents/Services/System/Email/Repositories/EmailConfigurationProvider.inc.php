<?php
/* --------------------------------------------------------------
   EmailConfigurationProvider.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmailConfigurationProvider
 */
class EmailConfigurationProvider
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $table = 'gx_configurations';
    
    /**
     * @var string
     */
    protected $keyField = 'key';
    
    /**
     * @var string
     */
    protected $valueField = 'value';
    
    
    /**
     * EmailConfigurationProvider constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns the email transport configuration value.
     *
     * @return string Configuration value of email transport.
     */
    public function emailTransport()
    {
        return $this->_getEmailConfig('EMAIL_TRANSPORT');
    }
    
    
    /**
     * Returns the smtp auth configuration value.
     *
     * @return string Configuration value of emails smtp auth.
     */
    public function auth()
    {
        return $this->_getEmailConfig('SMTP_AUTH');
    }
    
    
    /**
     * Returns the smtp backup server configuration value.
     *
     * @return string Configuration value of emails smtp backup server.
     */
    public function backupServer()
    {
        return $this->_getEmailConfig('SMTP_Backup_Server');
    }
    
    
    /**
     * Returns the smtp encryption configuration value.
     *
     * @return string Configuration value of emails smtp encryption.
     */
    public function encryption()
    {
        return $this->_getEmailConfig('SMTP_ENCRYPTION');
    }
    
    
    /**
     * Returns the smtp main server configuration value.
     *
     * @return string Configuration value of emails smtp main server.
     */
    public function mainServer()
    {
        return $this->_getEmailConfig('SMTP_MAIN_SERVER');
    }
    
    
    /**
     * Returns the smtp password configuration value.
     *
     * @return string Configuration value of emails smtp password.
     */
    public function password()
    {
        return $this->_getEmailConfig('SMTP_PASSWORD');
    }
    
    
    /**
     * Returns the smtp port configuration value.
     *
     * @return string Configuration value of emails smtp port.
     */
    public function port()
    {
        return $this->_getEmailConfig('SMTP_PORT');
    }
    
    
    /**
     * Returns the smtp user configuration value.
     *
     * @return string Configuration value of emails smtp username.
     */
    public function user()
    {
        return $this->_getEmailConfig('SMTP_USERNAME');
    }
    
    
    /**
     * Returns the send mail path configuration value.
     *
     * @return string Configuration value of send mail path.
     */
    public function sendMailPath()
    {
        return $this->_getEmailConfig('SENDMAIL_PATH');
    }
    
    
    /**
     * Returns the charset configuration value for emails.
     * $_SESSION['language_charset'] will be used if it is set.
     * If not, the charset of the default shop language is taken.
     *
     * @return string
     */
    public function charset()
    {
        if (array_key_exists('language_charset', $_SESSION)) {
            return $_SESSION['language_charset'];
        }
        $field           = 'language_charset';
        $defaultLanguage = $this->defaultLanguage();
        
        $result = $this->db->select($field)->from('languages')->where('code', $defaultLanguage)->get()->row_array();
        
        return array_key_exists($field, $result) ? $result[$field] : 'utf-8';
    }
    
    
    /**
     * Returns the language code configuration value.
     * The code is 'de', if $_SESSION['language'] is equal to 'german'.
     * Any other case will return en.
     *
     * @return string
     */
    public function languageCode()
    {
        return array_key_exists('language', $_SESSION) && $_SESSION['language'] === 'german' ? 'de' : 'en';
    }
    
    
    /**
     * Returns true if html emails should be used.
     * The configuration value will be read from configuration table.
     *
     * @return bool
     */
    public function useHtmlMail()
    {
        return $this->_getEmailConfig('EMAIL_USE_HTML') === 'true';
    }
    
    
    /**
     * Returns the default language configuration value.
     *
     * @return string Configuration value of default language.
     */
    protected function defaultLanguage()
    {
        return $this->_getEmailConfig('DEFAULT_LANGUAGE');
    }
    
    
    /**
     * Returns the email configuration value for the given key.
     * The configuration value will be read from the configurations table.
     *
     * @param string $key Key of expected configuration value.
     *
     * @return string Configuration value of given key.
     */
    protected function _getEmailConfig($key)
    {
        $result = $this->db->select($this->valueField)
            ->from($this->table)
            ->where($this->keyField, "configuration/$key")
            ->get()
            ->row_array();
        
        return array_key_exists($this->valueField, $result) ? $result[$this->valueField] : '';
    }
}