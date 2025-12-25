<?php
/* --------------------------------------------------------------
   ServerDetails.php 2018-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\ValueObjects;

/**
 * Class ServerDetails
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\ValueObjects
 */
class ServerDetails
{
    /**
     * @var PhpServerDetails
     */
    private $php;
    
    /**
     * @var MysqlServerDetails
     */
    private $mysql;
    
    /**
     * @var string
     */
    private $webserver;
    
    /**
     * @var string
     */
    private $os;
    
    
    /**
     * ServerDetails constructor.
     *
     * @param PhpServerDetails   $php
     * @param MysqlServerDetails $mysql
     * @param string             $webserver
     * @param string             $os
     */
    public function __construct(PhpServerDetails $php, MysqlServerDetails $mysql, $webserver, $os)
    {
        $this->php       = $php;
        $this->mysql     = $mysql;
        $this->webserver = $webserver;
        $this->os        = $os;
    }
    
    
    /**
     * Creates and returns a new ServerDetails instance.
     *
     * @param PhpServerDetails   $php
     * @param MysqlServerDetails $mysql
     * @param string             $webserver
     * @param string             $os
     *
     * @return ServerDetails
     */
    static function create(PhpServerDetails $php, MysqlServerDetails $mysql, $webserver, $os)
    {
        return new self($php, $mysql, $webserver, $os);
    }
    
    
    /**
     * Returns the php details.
     *
     * @return PhpServerDetails
     */
    public function php()
    {
        return $this->php;
    }
    
    
    /**
     * Returns the mysql details.
     *
     * @return MysqlServerDetails
     */
    public function mysql()
    {
        return $this->mysql;
    }
    
    
    /**
     * Returns the software name that is used for the web server.
     *
     * @return string
     */
    public function webserver()
    {
        return $this->webserver;
    }
    
    
    /**
     * Returns the name of the operating system.
     *
     * @return string
     */
    public function os()
    {
        return $this->os;
    }
}