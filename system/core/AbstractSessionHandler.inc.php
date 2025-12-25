<?php
/* --------------------------------------------------------------
   AbstractSessionHandler.inc.php 2018-07-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractSessionHandler
 *
 **/
abstract class AbstractSessionHandler extends SessionHandler
{
    protected $savePath;
    protected $name;
    protected $logger;
    protected $loggingEnabled = false;
    protected $useLogControl  = false;
    protected $logFile = '';

    const LOG_GROUP = 'widgets';
    const LOG_FILE = 'sessions-debug';
    
    
    public function __construct()
    {
        $this->logFile = DIR_FS_CATALOG . '/logfiles/' . self::LOG_FILE . '-' . LogControl::get_secure_token() . '.log';
        if ($this->loggingEnabled === true && $this->useLogControl === true)
        {
            $this->logger = LogControl::get_instance();
        }
    }
    
    protected function logNotice($message)
    {
        if($this->loggingEnabled === true)
        {
            if($this->useLogControl === true && $this->logger !== null)
            {
                $this->logger->notice($message, self::LOG_GROUP, self::LOG_FILE);
            }
            else
            {
                $logMessage = sprintf(
                    "%s | %s\n",
                    (new DateTime())->format('Y-m-d H:i:s.u'),
                    $message
                );
                file_put_contents($this->logFile, $logMessage, FILE_APPEND);
            }
        }
    }
    
    /**
     * Intercepts session id creation to set a global variable $session_id_created.
     *
     * This allows to detect newly created sessions elsewhere by checking if $GLOBALS['session_id_created'] is set.
     *
     * @return string
     */
    public function create_sid()
    {
        $sid = parent::create_sid();
        $GLOBALS['session_id_created'] = $sid;
        $this->logNotice('new session: ' . $sid);
        return $sid;
    }
    
    public function open($save_path, $session_name)
    {
        $this->savePath = $save_path;
        $this->name     = $session_name;
        $success        = parent::open($save_path, $session_name);
        $this->logNotice(sprintf('opened session, path = %s, name = %s, success = %s', $save_path, $session_name, $success ? 'yes' : 'no'));
        return $success;
    }
    
    public function read($session_id)
    {
        $sessionString = parent::read($session_id);
        $this->logNotice(sprintf('read %d bytes for session %s', strlen($sessionString), $session_id));
        return $sessionString;
    }

    public function write($session_id, $session_data)
    {
        $success = parent::write($session_id, $session_data);
        $this->logNotice(sprintf('wrote %s bytes for session %s (%s)', strlen($session_data), $session_id, $success ? 'OK' : 'FAIL'));
        return $success;
    }

    public function close()
    {
        $success = parent::close();
        $this->logNotice(sprintf('closed session (%s)', $success ? 'OK' : 'FAIL'));
        return $success;
    }
    
    public function gc($maxlifetime)
    {
        $this->logNotice(sprintf('garbage collection for maxlifetime = %s', $maxlifetime));
        return parent::gc($maxlifetime);
    }
}
