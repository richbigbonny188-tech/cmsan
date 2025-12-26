<?php
/* --------------------------------------------------------------
   LogFilesService.inc.php 2018-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LogFilesService
 */
class LogFilesService implements LogFilesServiceInterface
{
    /**
     * @var array
     */
    protected $blacklist = [
        '.',
        '..'
    ];
    
    /**
     * @var int
     */
    protected $threshold;
    
    /**
     * @var string
     */
    protected $root;
    
    
    /**
     * LogFilesService constructor.
     *
     * @param \IntType           $thresholdAsDays Maximum age in days of logfile until it gets removed.
     * @param \ExistingDirectory $root            Log files root directory.
     * @param array              $blacklist       List of blacklisted file names (e.g. .htaccess). Gets merged with "."
     *                                            and "..".
     */
    public function __construct(IntType $thresholdAsDays, ExistingDirectory $root, array $blacklist)
    {
        $this->threshold = $thresholdAsDays->asInt();
        $this->root      = $root->getAbsolutePath();
        $this->blacklist = array_merge($this->blacklist, $blacklist);
    }
    
    
    /**
     * Returns the deleted log files.
     *
     * @return string[] List of removed file names.
     */
    public function deleteOldLogFiles()
    {
        $deleted = [];
        
        foreach (new IteratorIterator(new DirectoryIterator($this->root)) as $file) {
            /** @var \DirectoryIterator $file */
            if ($this->_isBlacklisted($file) || $this->_isNotDeprecated($file)) {
                continue;
            }
            $removed = @unlink($file->getPathname());
            if ($removed) {
                $deleted['deleted'][] = $file->getFilename();
            } else {
                $deleted['failed'][] = $file->getFilename();
            }
        }
        
        return $deleted;
    }
    
    
    /**
     * Checks and returns true if given file is blacklisted.
     *
     * @param \DirectoryIterator $file File to be checked.
     *
     * @return bool True if file is blacklisted.
     */
    protected function _isBlacklisted(DirectoryIterator $file)
    {
        return in_array($file->getFilename(), $this->blacklist);
    }
    
    
    /**
     * Checks and returns true if given file is NOT deprecated (Never than $threshold days).
     *
     * @param \DirectoryIterator $file File to be checked.
     *
     * @return bool True if file is NOT deprecated.
     */
    protected function _isNotDeprecated(DirectoryIterator $file)
    {
        return $file->getMTime() > strtotime('-' . $this->threshold . ' days');
    }
}
