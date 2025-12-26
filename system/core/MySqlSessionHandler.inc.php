<?php
/* --------------------------------------------------------------
   MySqlSessionHandler.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractSessionHandler');

class MySqlSessionHandler extends AbstractSessionHandler
{
    protected $db;
    
    public function __construct()
    {
        $this->loggingEnabled = true;
        $this->db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        parent::__construct();
    }
    
    public function read($session_id)
    {
        $sessionRow = $this->db->get_where(
            'sessions',
            ['name' => $this->name, 'path' => $this->savePath, 'session_id' => $session_id]
        )->row_array();
        $sessionString = $sessionRow === null ? '' : $sessionRow['data'];
        $this->logNotice(sprintf('read %d bytes for session %s', strlen($sessionString), $session_id));
        $decodedSessionString = @base64_decode($sessionString);
        $unpackedSessionString = $decodedSessionString !== false ? @gzdecode($decodedSessionString) : $sessionString;
        $sessionString = $unpackedSessionString === false ? $sessionString : $unpackedSessionString;
        $this->logNotice(sprintf('unpacked %d bytes', strlen($sessionString)));
        return $sessionString;
    }
    
    public function write($session_id, $session_data)
    {
        $startTime = microtime(true);
        $this->logNotice('start transaction for session write');
        $this->db->trans_start();
        $query = $this->db->select()
                          ->from('sessions')
                          ->where(['name' => $this->name, 'path' => $this->savePath, 'session_id' => $session_id])
                          ->get_compiled_select();
        $sessionRow = $this->db->query("{$query} FOR UPDATE")->row_array();
        if(!empty($sessionRow))
        {
            $this->logNotice('merging session data');
            session_decode($sessionRow['data']);
            $dbSession = $_SESSION;
            session_decode($session_data);
            $currentSession = $_SESSION;
            $_SESSION = array_merge($dbSession, $currentSession);
            $session_data = session_encode();
            $session_data = base64_encode(gzencode($session_data));
            $success = $this->db->set('data', $session_data)->where(
                ['name' => $this->name, 'path' => $this->savePath, 'session_id' => $session_id]
            )->update('sessions');
            $this->db->trans_complete();
        }
        else
        {
            $this->db->trans_complete();
            $session_data = base64_encode(gzencode($session_data));
            $success = $this->db->set(
                ['name' => $this->name, 'path' => $this->savePath, 'session_id' => $session_id, 'data' => $session_data]
            )->insert('sessions');
        }
        $elapsedTime = microtime(true) - $startTime;
        $this->logNotice(sprintf('wrote %s bytes for session %s (%s), took %.6s', strlen($session_data), $session_id, $success ? 'OK' : 'FAIL', $elapsedTime));
        return $success;
    }

    public function destroy($session_id)
    {
        $success = $this->db->delete('sessions', ['name' => $this->name, 'path' => $this->savePath, 'session_id' => $session_id]);
        $this->logNotice(sprintf('destroyed %s (%s)', $session_id, $success ? 'OK' : 'FAIL'));
        return $success;
    }
    
    public function gc($maxlifetime)
    {
        $this->logNotice(sprintf('garbage collection for maxlifetime = %s', $maxlifetime));
        $this->db->where('last_modified <= date_sub(current_timestamp, interval ' . (int)$maxlifetime . ' second)')
            ->delete('sessions');
        $this->logNotice(sprintf('deleted %d stale sessions', $this->db->affected_rows()));
        return true;
    }
}
