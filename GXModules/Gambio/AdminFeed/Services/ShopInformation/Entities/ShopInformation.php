<?php
/* --------------------------------------------------------------
   ShopInformation.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Entities;

use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\FileSystemDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ModulesDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ServerDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ShopDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\ThemeDetails;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdatesDetails;

/**
 * Class ShopInformation
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Entities
 */
class ShopInformation
{
    /**
     * @var ShopDetails
     */
    private $shop;
    
    /**
     * @var ServerDetails
     */
    private $server;
    
    /**
     * @var ModulesDetails
     */
    private $modules;
    
    /**
     * @var ThemeDetails
     */
    private $themes;
    
    /**
     * @var FileSystemDetails
     */
    private $filesystem;
    
    /**
     * @var UpdatesDetails
     */
    private $updates;
    
    /**
     * @var int
     */
    private $version;
    
    
    /**
     * ShopInformation constructor.
     *
     * @param ShopDetails       $shop
     * @param ServerDetails     $server
     * @param ModulesDetails    $modules
     * @param ThemeDetails      $themes
     * @param FileSystemDetails $filesystem
     * @param UpdatesDetails    $updates
     * @param int               $version
     */
    public function __construct(
        ShopDetails $shop,
        ServerDetails $server,
        ModulesDetails $modules,
        ThemeDetails $themes,
        FileSystemDetails $filesystem,
        UpdatesDetails $updates,
        $version = 1
    ) {
        $this->shop       = $shop;
        $this->server     = $server;
        $this->modules    = $modules;
        $this->themes     = $themes;
        $this->filesystem = $filesystem;
        $this->updates    = $updates;
        $this->version    = $version;
    }
    
    
    /**
     * Creates and returns a new ShopInformation instance.
     *
     * @param ShopDetails       $shop
     * @param ServerDetails     $server
     * @param ModulesDetails    $modules
     * @param ThemeDetails      $themes
     * @param FileSystemDetails $filesystem
     * @param UpdatesDetails    $updates
     * @param int               $version
     *
     * @return ShopInformation
     */
    static function create(
        ShopDetails $shop,
        ServerDetails $server,
        ModulesDetails $modules,
        ThemeDetails $themes,
        FileSystemDetails $filesystem,
        UpdatesDetails $updates,
        $version = 1
    ) {
        return new self($shop, $server, $modules, $themes, $filesystem, $updates, $version);
    }
    
    
    /**
     * Returns the shop details.
     *
     * @return ShopDetails
     */
    public function shop()
    {
        return $this->shop;
    }
    
    
    /**
     * Returns the server details.
     *
     * @return ServerDetails
     */
    public function server()
    {
        return $this->server;
    }
    
    
    /**
     * Returns the modules details.
     *
     * @return ModulesDetails
     */
    public function modules()
    {
        return $this->modules;
    }
    
    
    /**
     * Returns the theme details.
     *
     * @return ThemeDetails
     */
    public function themes()
    {
        return $this->themes;
    }
    
    
    /**
     * Returns the filesystem details.
     *
     * @return FileSystemDetails
     */
    public function filesystem()
    {
        return $this->filesystem;
    }
    
    
    /**
     * Returns the updates details.
     *
     * @return UpdatesDetails
     */
    public function updates()
    {
        return $this->updates;
    }
    
    
    /**
     * Returns the version of these server information object.
     *
     * @return int
     */
    public function version()
    {
        return $this->version;
    }
}