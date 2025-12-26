<?php
/* --------------------------------------------------------------
  GProtector.inc.php 2022-12-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use Exception;

class GProtector
{
    private $secureToken        = '';
    private $filterArray        = [];
    private $logHeaderTemplate  = '';
    private $separator          = "\r\n";
    private $logConnectorsArray = [];
    
    /**
     * @var FilterReader
     */
    private $reader;
    
    /**
     * @var FilterCache
     */
    private $cache;
    
    
    /**
     * GProtector constructor.
     *
     * @param $reader
     * @param $cache
     */
    public function __construct($reader, $cache)
    {
        $this->reader = $reader;
        $this->cache  = $cache;
        
        $this->setSecureToken();
        $this->setLogHeaderTemplate(
            "===========================================================\nIP: {IP}\nDatum: {DATETIME}\nScript: {SCRIPT}\nNachricht: {MESSAGE}\n\n"
        );
        $this->initLogConnectors();
        $this->loadFunctions();
    }
    
    
    /**
     * This function starts the GProtector filters
     * @throws Exception
     */
    public function start()
    {
        $this->cache->renew();
        $this->applyFilters();
        $this->blockForbiddenIps();
    }
    
    
    /**
     * @throws Exception
     */
    private function applyFilters()
    {
        $filters = $this->readFilterFiles();
        $this->addFilters($filters);
        $this->filter();
    }
    
    
    /**
     * @param FilterCollection $filters
     */
    private function addFilters(FilterCollection $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }
    
    
    /**
     * @return FilterCollection
     * @throws Exception
     */
    private function readFilterFiles()
    {
        $cacheFileExists = file_exists(GAMBIO_PROTECTOR_CACHE_DIR . GAMBIO_PROTECTOR_CACHE_FILERULES_FILENAME);
        
        $rawFilters = $cacheFileExists ? $this->cache->getCachedFilterRules() : $this->reader->getFallbackFilterRules();
        $rawFilters = array_merge($rawFilters, $this->reader->getCustomFilterRules());
        
        
        return FilterCollection::fromData($rawFilters);
    }
    
    
    /**
     *
     */
    private function blockForbiddenIps()
    {
        if ($this->searchIpInBlacklist($this->getUserIp()) === true) {
            $this->blockIp();
        }
    }
    
    
    /**
     *
     */
    private function initLogConnectors()
    {
        $filesArray = glob(GAMBIO_PROTECTOR_CONNECTORS_DIR . '*.php');
        
        if (is_array($filesArray)) {
            foreach ($filesArray as $file) {
                include_once $file;
            }
        }
    }
    
    
    /**
     * Return the visitor's IP addresses
     *
     * @return array Visitor's IP addresses
     */
    private function getUserIp()
    {
        $headersToCheck = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];
    
        $ipList = [];
        foreach ($headersToCheck as $headerName) {
            if (!empty($_SERVER[$headerName])) {
                if (strpos($_SERVER[$headerName], ",") === false) {
                    $ipList[] = $_SERVER[$headerName];
                    continue;
                }
                
                // Removes the white space after the comma
                $currentHeader = preg_replace('/,\s/', ',', $_SERVER[$headerName]);
                $ipList = array_merge($ipList, explode(',', $currentHeader));
            }
        }
    
        return $ipList;
    }
    
    
    /**
     * Search the given IP in blacklist and returns true if it is in the blacklist.
     *
     * @param array $userIpList User IPs to check
     *
     * @return bool OK:false | blocked IP: true
     *
     */
    private function searchIpInBlacklist($userIpList)
    {
	    if (!file_exists($this->getIpBlacklistPath())) {
		    return false;
	    }
	
	    if (!is_readable($this->getIpBlacklistPath())) {
		    $this->log('Can not read IP-blacklist', 'gprotector_error', 'error');
		    return false;
	    }
	
	    $fileHandle = fopen($this->getIpBlacklistPath(), 'r');
	    while (!feof($fileHandle)) {
		    $blockedIp = fgets($fileHandle);
		    $blockedIp = trim($blockedIp);
		
		    if ($blockedIp === '') {
			    continue;
		    }
		
		    foreach ($userIpList as $userIp) {
			    if (strpos(trim($userIp), $blockedIp) === 0) {
				    fclose($fileHandle);
				    return true;
			    }
		    }
	    }
	    fclose($fileHandle);
	
	    return false;
    }
    
    
    /**
     * Sends 403 Header to any blocked IPs
     */
    private function blockIp()
    {
        header("HTTP/1.0 403 forbidden");
        echo 'forbidden';
        exit;
    }
    
    
    private function addFilter(Filter $filter)
    {
        $this->filterArray[$filter->key()] = [
            'script_name_array' => $filter->scriptName(),
            'variables_array'   => $filter->variables(),
            'function'          => $filter->method(),
            'severity'          => $filter->severity()
        ];
    }
    
    
    private function filter()
    {
        if (is_array($this->filterArray)) {
            foreach ($this->filterArray as $filterName => $dataArray) {
                if (isset($valueReference)) {
                    unset($valueReference);
                }
                
                if (is_array($dataArray) && isset($dataArray['script_name_array'])
                    && is_array(
                        $dataArray['script_name_array']
                    )) {
                    foreach ($dataArray['script_name_array'] as $scriptPath) {
                        if ($this->isScript($scriptPath->scriptName()) === true) {
                            if (isset($dataArray['function'])) {
                                $function       = (string)$dataArray['function'];
                                $functionPrefix = $this->getFunctionPrefix();
                                $function       = $functionPrefix . $function;
                                
                                if (function_exists($function)) {
                                    if (isset($dataArray['variables_array'])
                                        && is_array(
                                            $dataArray['variables_array']
                                        )) {
                                        /**
                                         * @var Variable $variablesArray
                                         */
                                        foreach ($dataArray['variables_array'] as $variablesArray) {
                                            $variables = [];
                                            
                                            if ($variablesArray->isSubCategory()) {
                                                foreach ($variablesArray->properties() as $property) {
                                                    $variables[] = '_' . $variablesArray->type() . '["' . $variablesArray->subCategory() . '"]' . '["' . $property . '"]';
                                                }
                                            } else {
                                                $variables[] = '_' . $variablesArray->type() . '["' . $variablesArray->properties() . '"]';
                                            }
                                            
                                            
                                            foreach ($variables as $variable) {
                                                $variableString = (string)$variable;
                                                
                                                $arrayBracketPos    = (int)strpos($variableString, '[');
                                                $variableNameEndPos = strlen($variableString);
                                                
                                                if ($arrayBracketPos > 0) {
                                                    $variableNameEndPos = $arrayBracketPos;
                                                }
                                                
                                                if ($variableNameEndPos > 0) {
                                                    $variableName = substr($variableString, 0, $variableNameEndPos);
                                                    
                                                    global $$variableName;
                                                    
                                                    $variableReference =& $$variableName;
                                                    
                                                    preg_match_all(
                                                        '/\[("|\')?([^"\'\]]+)("|\')?]/',
                                                        $variableString,
                                                        $matchesArray
                                                    );
                                                    
                                                    if (isset($matchesArray[2]) && !empty($matchesArray[2])) {
                                                        foreach ($matchesArray[2] as $key) {
                                                            if (!isset($valueReference)
                                                                && isset($variableReference[$key])) {
                                                                $valueReference =& $variableReference[$key];
                                                            } elseif (isset($valueReference) && is_array($valueReference)) {
                                                                $valueReference =& $valueReference[$key];
                                                            }
                                                        }
                                                    } else {
                                                        $valueReference = $variableReference;
                                                    }
                                                    
                                                    if (isset($valueReference) && $valueReference !== '') {
                                                        // run filter
                                                        $variableCopy   = $valueReference;
                                                        $valueReference = call_user_func($function, $valueReference);
                                                        if ($variableCopy != $valueReference) {
                                                            $this->log(
                                                                'Die Regel "' . $filterName
                                                                . '" hat einen unerwarteten Variablenwert erkannt und erfolgreich gefiltert.',
                                                                'security',
                                                                $dataArray['severity']
                                                            );
                                                            if (is_array($variableCopy) || is_object($variableCopy)) {
                                                                $this->log(
                                                                    "unerwarteter Variablenwert\r\nFilterregel: "
                                                                    . $filterName
                                                                    . "\r\nVariable: $$variableString\rnvorher: " . print_r(
                                                                        $variableCopy,
                                                                        true
                                                                    ) . "\r\nnachher: " . print_r($valueReference, true),
                                                                    'security_debug',
                                                                    $dataArray['severity']
                                                                );
                                                            } else {
                                                                $this->log(
                                                                    "unerwarteter Variablenwert\r\nFilterregel: "
                                                                    . $filterName
                                                                    . "\r\nVariable: $$variableString\r\nvorher: "
                                                                    . $variableCopy . "\r\nnachher: " . $valueReference,
                                                                    'security_debug',
                                                                    $dataArray['severity']
                                                                );
                                                            }
                                                        }
                                                    }
                                                    
                                                    if (isset($valueReference)) {
                                                        unset($valueReference);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $this->log('filter variables are missing', 'gprotector_error', 'error');
                                    }
                                } else {
                                    $this->log(
                                        'filter function "' . $function . '" does not exist',
                                        'gprotector_error',
                                        'error'
                                    );
                                }
                            } else {
                                $this->log('filter function is not set', 'gprotector_error', 'error');
                            }
                        }
                    }
                } else {
                    $this->log('filter data is missing', 'gprotector_error', 'error');
                }
            }
        } else {
            $this->log('v_filter_array is not set', 'gprotector_error', 'error');
        }
        
        return true;
    }
    
    
    private function loadFunctions()
    {
        $filesArray = glob(GAMBIO_PROTECTOR_FUNCTIONS_DIR . '*.php');
        
        if (is_array($filesArray)) {
            foreach ($filesArray as $filepath) {
                include_once $filepath;
            }
            
            return true;
        } else {
            $this->log('No functions found', 'gprotector_error', 'warning');
        }
        
        return false;
    }
    
    
    private function getRunningScriptPath()
    {
        $scriptPath = false;
        
        $backtraceArray = debug_backtrace();
        if (is_array($backtraceArray)) {
            $runningScriptDataArray = array_pop($backtraceArray);
            $scriptPath             = $runningScriptDataArray['file'];
            
            if (defined('GAMBIO_PROTECTOR_BASE_DIR') && is_string(GAMBIO_PROTECTOR_BASE_DIR)) {
                $scriptPath = str_replace(GAMBIO_PROTECTOR_BASE_DIR, '', $scriptPath);
            }
        }
        
        if ($scriptPath === false) {
            $this->log('script name could not be determined', 'gprotector_error', 'warning');
        }
        
        return $scriptPath;
    }
    
    
    private function getFunctionPrefix()
    {
        $functionPrefix = 'gprotector_';
        
        if (defined('GAMBIO_PROTECTOR_FUNCTION_PREFIX')) {
            $prefix = preg_replace('/[^a-zA-Z_]/', '', trim((string)GAMBIO_PROTECTOR_FUNCTION_PREFIX));
            if ($prefix != '') {
                $functionPrefix = $prefix;
            }
        }
        
        return $functionPrefix;
    }
    
    
    private function getTokenPrefix()
    {
        $tokenPrefix = 'gprotector_';
        
        if (defined('GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX')) {
            $prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', trim((string)GAMBIO_PROTECTOR_TOKEN_FILE_PREFIX));
            if ($prefix != '') {
                $tokenPrefix = $prefix;
            }
        }
        
        return $tokenPrefix;
    }
    
    
    private function setSecureToken()
    {
        $filesArray = glob(GAMBIO_PROTECTOR_TOKEN_DIR . $this->getTokenPrefix() . '*');
        
        if (is_array($filesArray) && count($filesArray)) {
            foreach ($filesArray as $filepath) {
                $tokenFilename = basename($filepath);
                $token         = str_replace($this->getTokenPrefix(), '', $tokenFilename);
                
                if (strlen($token) > 0) {
                    $this->secureToken = $token;
                }
            }
        } elseif (is_writable(GAMBIO_PROTECTOR_TOKEN_DIR)) {
            $token     = md5(time() . rand());
            $tokenFile = GAMBIO_PROTECTOR_TOKEN_DIR . $this->getTokenPrefix() . $token;
            
            if (function_exists('file_put_contents')) {
                @file_put_contents($tokenFile, 'empty');
            } else {
                $fp = @fopen($tokenFile, 'w');
                @fwrite($fp, 'empty');
                @fclose($fp);
            }
            
            if (!file_exists($tokenFile)) {
                return false;
            } else {
                $this->secureToken = $token;
            }
        }
        
        return true;
    }
    
    
    private function getSecureToken()
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '', (trim((string)$this->secureToken)));
    }
    
    
    private function writeCustomLog($message, $type, $severity = 'error')
    {
        $receivedMessage = (string)$message;
        $messageDetails  = $this->prepareLogMessage($receivedMessage);
        
        if (strpos($receivedMessage, $this->separator) !== false) {
            $receivedMessage = substr($receivedMessage, 0, strpos($receivedMessage, $this->separator));
        }
        
        $logSuccess = 1;
        
        foreach ($this->logConnectorsArray as $GProtectorLogConnector) {
            $errorType  = 'GPROTECTOR ' . strtoupper($severity);
            $logSuccess &= $GProtectorLogConnector->log(
                $receivedMessage,
                'security',
                $type,
                $severity,
                $errorType,
                $messageDetails
            );
        }
        
        if (!$logSuccess) {
            $this->writeLog($message, $type, $severity);
        }
        
        return true;
    }
    
    
    private function prepareLogMessage($string)
    {
        $receivedString  = (string)$string;
        $preparedMessage = '';
        
        if (strpos($receivedString, $this->separator) !== false) {
            $preparedMessage = str_replace(
                "'",
                "\\'",
                substr(
                    $receivedString,
                    strpos($receivedString, $this->separator) + strlen($this->separator)
                )
            );
        }
        
        return $preparedMessage;
    }
    
    
    private function writeLog($message, $type, $severity = 'error')
    {
        $receivedMessage = (string)$message;
        $logFilename     = $this->getLogFilename($type);
        if ($logFilename !== false) {
            $logFilePath  = GAMBIO_PROTECTOR_LOG_DIR . $logFilename;
            $writtenBytes = false;
            
            if (is_dir(GAMBIO_PROTECTOR_LOG_DIR)
                && is_writable(GAMBIO_PROTECTOR_LOG_DIR)
                && ((file_exists($logFilePath)
                     && is_writeable($logFilePath))
                    || (!file_exists($logFilePath)))) {
                if (function_exists('file_put_contents')) {
                    $writtenBytes = @file_put_contents(
                        $logFilePath,
                        $this->getSubstitutedLogContent(
                            $this->logHeaderTemplate,
                            $receivedMessage,
                            $severity
                        ),
                        FILE_APPEND | LOCK_EX
                    );
                } else {
                    $fp           = @fopen($logFilePath, 'a');
                    $writtenBytes = @fwrite(
                        $fp,
                        $this->getSubstitutedLogContent(
                            $this->logHeaderTemplate,
                            $receivedMessage,
                            $severity
                        )
                    );
                    @fclose($fp);
                }
                
                if ((defined('GAMBIO_PROTECTOR_GZIP_LOG') && GAMBIO_PROTECTOR_GZIP_LOG === true)
                    || defined(
                           'GAMBIO_PROTECTOR_GZIP_LOG'
                       ) === false) {
                    $maxFilesize = 1 * 1024 * 1024; // standard: 1 megabyte
                    if (defined('GAMBIO_PROTECTOR_LOG_MAX_FILESIZE') && (double)GAMBIO_PROTECTOR_LOG_MAX_FILESIZE > 0) {
                        $maxFilesize = (double)GAMBIO_PROTECTOR_LOG_MAX_FILESIZE * 1024 * 1024;
                    }
                    
                    // compress logfile if larger than GAMBIO_PROTECTOR_LOG_MAX_FILESIZE megabyte
                    if (filesize($logFilePath) > $maxFilesize) {
                        $fp = @fopen($logFilePath, 'r+');
                        if ($fp !== false) {
                            @date_default_timezone_set('Europe/Berlin');
                            $compressedFilePath = substr($logFilePath, 0, strpos($logFilePath, ".")) . '-' . date(
                                    'Ymd_His'
                                ) . '.log.gz';
                            $compressedFile     = @gzopen($compressedFilePath, 'w9');
                            if ($compressedFile !== false) {
                                @gzwrite($compressedFile, fread($fp, filesize($logFilePath)));
                                @gzclose($compressedFile);
                                
                                // delete content of log which was compressed before
                                @ftruncate($fp, 0);
                            }
                            @fclose($fp);
                        }
                    }
                }
            }
            
            if ($writtenBytes === false || $writtenBytes == 0) {
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    
    private function log($message, $type, $severity = 'error')
    {
        if (!empty($this->logConnectorsArray)) {
            return $this->writeCustomLog($message, $type, $severity);
        }
        
        return $this->writeLog($message, $type, $severity);
    }
    
    
    private function getLogFilename($type)
    {
        $receivedType = basename(trim((string)$type));
        $secureToken  = $this->getSecureToken();
        
        if ($receivedType != '' && $secureToken != '') {
            $logFilename = $receivedType . '-' . $secureToken . '.log';
            
            return $logFilename;
        }
        
        return false;
    }
    
    
    private function setLogHeaderTemplate($template)
    {
        $receivedTemplate        = (string)$template;
        $this->logHeaderTemplate = $receivedTemplate;
    }
    
    
    private function getSubstitutedLogContent(
        $template,
        $message = '',
        $severity = 'error',
        $logFilename = 'security',
        $messageDetails = ''
    ) {
        @date_default_timezone_set('Europe/Berlin');
        
        $receivedTemplate       = (string)$template;
        $receivedMessage        = (string)$message;
        $receivedSeverity       = (string)$severity;
        $receivedLogFilename    = (string)$logFilename;
        $receivedMessageDetails = (string)$messageDetails;
        
        $receivedTemplate = $this->substitute($receivedTemplate, '{IP}', implode(',', $this->getUserIp()));
        $receivedTemplate = $this->substitute($receivedTemplate, '{DATETIME}', date('Y-m-d H:i:s'));
        $receivedTemplate = $this->substitute($receivedTemplate, '{MESSAGE}', $receivedMessage);
        $receivedTemplate = $this->substitute($receivedTemplate, '{SCRIPT}', $this->getRunningScriptPath());
        $receivedTemplate = $this->substitute($receivedTemplate, '{LOGFILE_NAME}', (string)$receivedLogFilename);
        $receivedTemplate = $this->substitute($receivedTemplate, '{SEVERITY}', $receivedSeverity);
        $receivedTemplate = $this->substitute($receivedTemplate, '{ERROR_TYPE}', 'GPROTECTOR ' . strtoupper($severity));
        $receivedTemplate = $this->substitute($receivedTemplate, '{MESSAGE_DETAILS}', $receivedMessageDetails);
        
        return $receivedTemplate;
    }
    
    
    private function substitute($content, $placeHolder, $substitution = '')
    {
        $receivedContent = (string)$content;
        
        if (strpos($content, $placeHolder) !== false) {
            $receivedContent = str_replace((string)$placeHolder, (string)$substitution, $receivedContent);
        }
        
        return $receivedContent;
    }
    
    
    private function isScript($scriptPath)
    {
        $receivedScriptPath = (string)$scriptPath;
        if ($this->getRunningScriptPath() == $receivedScriptPath) {
            return true;
        }
        
        return false;
    }
    
    
    private function getIpBlacklistPath()
    {
        $dir = __DIR__ . '/';
        
        if (defined('GAMBIO_PROTECTOR_DIR') && is_string(GAMBIO_PROTECTOR_DIR) && @is_dir(GAMBIO_PROTECTOR_DIR)) {
            $dir = GAMBIO_PROTECTOR_DIR;
        }
        
        return $dir . 'ip_blacklist.txt';
    }
}
