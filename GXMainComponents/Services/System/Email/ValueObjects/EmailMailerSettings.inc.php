<?php
/* --------------------------------------------------------------
   EmailMailerSettings.inc.php 2018-03-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EmailMailerSettings
 */
class EmailMailerSettings
{
    /**
     * @var string
     */
    protected $charset;
    
    /**
     * @var string
     */
    protected $languageCode;
    
    /**
     * @var string
     */
    protected $languagePath;
    
    /**
     * @var string
     */
    protected $emailTransport;
    
    /**
     * @var string
     */
    protected $mainServer;
    
    /**
     * @var string
     */
    protected $backupServer;
    
    /**
     * @var string
     */
    protected $auth;
    
    /**
     * @var string
     */
    protected $user;
    
    /**
     * @var string
     */
    protected $password;
    
    /**
     * @var string
     */
    protected $port;
    
    /**
     * @var string
     */
    protected $encryption;
    
    /**
     * @var string
     */
    protected $sendMailPath;
    
    /**
     * @var bool
     */
    protected $useHtmlMail;
    
    
    /**
     * EmailMailerSettings constructor.
     *
     * @param \StringType $charset        Charset setting value.
     * @param \StringType $languageCode   Language code setting value.
     * @param \StringType $languagePath   Language path setting value.
     * @param \StringType $emailTransport Email transport setting value.
     * @param \StringType $mainServer     Main server setting value.
     * @param \StringType $backupServer   Backup server setting value.
     * @param \StringType $auth           Auth setting value.
     * @param \StringType $user           User setting value.
     * @param \StringType $password       Password setting value.
     * @param \StringType $port           Port setting value.
     * @param \StringType $encryption     Encryption setting value.
     * @param \StringType $sendMailPath   Send mail path setting value.
     * @param \BoolType   $useHtmlMail    Use html format for email?
     */
    public function __construct(
        \StringType $charset,
        \StringType $languageCode,
        \StringType $languagePath,
        \StringType $emailTransport,
        \StringType $mainServer,
        \StringType $backupServer,
        \StringType $auth,
        \StringType $user,
        \StringType $password,
        \StringType $port,
        \StringType $encryption,
        \StringType $sendMailPath,
        \BoolType $useHtmlMail
    ) {
        $this->charset        = $charset->asString();
        $this->languageCode   = $languageCode->asString();
        $this->languagePath   = $languagePath->asString();
        $this->emailTransport = $emailTransport->asString();
        $this->mainServer     = $mainServer->asString();
        $this->backupServer   = $backupServer->asString();
        $this->auth           = $auth->asString();
        $this->user           = $user->asString();
        $this->password       = $password->asString();
        $this->port           = $port->asString();
        $this->encryption     = $encryption->asString();
        $this->sendMailPath   = $sendMailPath->asString();
        $this->useHtmlMail    = $useHtmlMail->asBool();
    }
    
    
    /**
     * Returns the email charset setting value.
     *
     * @return string
     */
    public function charset()
    {
        return $this->charset;
    }
    
    
    /**
     * Returns the email language code setting value.
     *
     * @return string
     */
    public function languageCode()
    {
        return $this->languageCode;
    }
    
    
    /**
     * Returns the email language path setting value.
     *
     * @return string
     */
    public function languagePath()
    {
        return $this->languagePath;
    }
    
    
    /**
     * Returns the email transport setting value.
     *
     * @return string
     */
    public function emailTransport()
    {
        return $this->emailTransport;
    }
    
    
    /**
     * Returns the email main server setting value.
     *
     * @return string
     */
    public function mainServer()
    {
        return $this->mainServer;
    }
    
    
    /**
     * Returns the email backup server setting value.
     *
     * @return string
     */
    public function backupServer()
    {
        return $this->backupServer;
    }
    
    
    /**
     * Returns the email auth setting value.
     *
     * @return string
     */
    public function auth()
    {
        return $this->auth;
    }
    
    
    /**
     * Returns the email user setting value.
     *
     * @return string
     */
    public function user()
    {
        return $this->user;
    }
    
    
    /**
     * Returns the email password setting value.
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }
    
    
    /**
     * Returns the email port setting value.
     *
     * @return string
     */
    public function port()
    {
        return $this->port;
    }
    
    
    /**
     * Returns the email encryption setting value.
     *
     * @return string
     */
    public function encryption()
    {
        return $this->encryption;
    }
    
    
    /**
     * Returns the email send mail path setting value.
     *
     * @return string
     */
    public function sendMailPath()
    {
        return $this->sendMailPath;
    }
    
    
    /**
     * Returns true if email should be send in html format and false otherwise.
     *
     * @return bool
     */
    public function useHtmlMail()
    {
        return $this->useHtmlMail;
    }
}
