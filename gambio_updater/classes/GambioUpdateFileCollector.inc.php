<?php
/*--------------------------------------------------------------
   GambioUpdateFileCollector.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

/**
 * Class GambioUpdateFileCollector
 */
class GambioUpdateFileCollector
{
    private string $updatesDirectory;
    
    
    public function __construct()
    {
        $this->updatesDirectory = dirname(__DIR__) . '/updates/';
    }
    
    
    /**
     * list of all dependent sub update files
     *
     * @param string $updateDirectory
     *
     * @return string[] list of relative paths in the given $updateDirectory
     */
    public function getDependentUpdateFiles(string $updateDirectory): array
    {
        $absolutePath = $this->updatesDirectory . "$updateDirectory/dependent";
        $files        = [];
        
        if (is_dir($absolutePath)) {
    
            $files = scandir($absolutePath);
            $files = array_filter($files, static fn(string $path): bool => (bool)preg_match('#\.php$#', $path));
            sort($files, SORT_STRING);
            $files = array_map(fn(string $path): string => 'dependent/' . $path, $files);
        }
        
        return $files;
    }
    
    /**
     * list of all independent sub update files
     *
     * @param string $updateDirectory
     *
     * @return string[] list of relative paths in the given $updateDirectory
     */
    public function getIndependentUpdateFiles(string $updateDirectory): array
    {
        $absolutePath = $this->updatesDirectory . "$updateDirectory/independent";
        $files        = [];
    
        if (is_dir($absolutePath)) {
        
            $files = scandir($absolutePath);
            $files = array_filter($files, static fn(string $path): bool => (bool)preg_match('#\.sql$#', $path));
            sort($files, SORT_STRING);
            $files = array_map(fn(string $path): string => 'independent/' . $path, $files);
        }
    
        return $files;
    }
}