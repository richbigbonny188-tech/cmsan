<?php
/* --------------------------------------------------------------
   User.php 2020-02-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Model;

/**
 * Class User
 *
 * @package Gambio\Core\Auth\Entities
 */
class User
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     */
    private $email;
    
    /**
     * @var string
     */
    private $passwordHash;
    
    
    /**
     * User constructor.
     *
     * @param int    $id
     * @param string $email
     * @param string $passwordHash
     */
    public function __construct(int $id, string $email, string $passwordHash)
    {
        $this->id           = $id;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }
    
    
    /**
     * @return string
     */
    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
}