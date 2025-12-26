<?php
/* --------------------------------------------------------------
   Registry.inc.php 2022-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Registry
{
    /**
     * @var array 
     */
    private $files = array();


    /**
     * @param string $className
     * @param string $path
     *
     * @return bool
     */
    public function set($className, $path)
    {
        if (!array_key_exists($className, $this->files)) {
            $this->files[$className] = $path;

            return true;
        }

        if ($this->files[$className] === $path) {
            return true;
        }

        if (is_string($this->files[$className])) {
            $this->files[$className] = [$this->files[$className], $path];

            return true;
        }

        if (!in_array($path, $this->files[$className], true)) {
            $this->files[$className][] = $path;
        }

        return true;
    }


    /**
     * @param string $className
     *
     * @return string|null
     */
    public function get($className)
    {
        if (!array_key_exists($className, $this->files)) {
            return null;
        }
        
        if (is_string($this->files[$className])) {
            return $this->files[$className];
        }

        if (is_array($this->files[$className])) {
            $applicationRunMode = defined('APPLICATION_RUN_MODE') ? APPLICATION_RUN_MODE : 'frontend';

            foreach ($this->files[$className] as $path) {
                if ($applicationRunMode === 'backend' && strpos($path, DIR_FS_CATALOG . 'admin/') === 0) {
                    return $path;
                }
            }

            return $path;
        }

        return null;
    }


    /**
     * @return array
     */
    public function get_all_data()
    {
        $registry = [];

        foreach ($this->files as $className => $paths) {
            if (is_string($paths)) {
                $registry[$className] = $paths;
                continue;
            }

            if (is_array($paths)) {
                $applicationRunMode = defined('APPLICATION_RUN_MODE') ? APPLICATION_RUN_MODE : 'frontend';

                foreach ($paths as $path) {
                    if ($applicationRunMode === 'backend' && strpos($path, DIR_FS_CATALOG . 'admin/') === 0) {
                        $registry[$className] = $path;
                        continue(2);
                    }

                    $registry[$className] = $path;
                }
            }
        }

        return $registry;
    }
}
