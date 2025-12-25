<?php
/* --------------------------------------------------------------
   UserRepository.php 2020-02-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Repositories;

use Gambio\Core\Auth\Exceptions\UserNotFoundException;
use Gambio\Core\Auth\Model\User;

/**
 * Class UserRepository
 *
 * @package Gambio\Core\Auth\Repositories
 */
class UserRepository
{
    /**
     * @var UserReader
     */
    private $reader;
    
    
    /**
     * UserRepository constructor.
     *
     * @param UserReader $reader
     */
    public function __construct(UserReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @param string $email
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function getUserByEmail(string $email): User
    {
        $userData = $this->reader->getUserByEmail($email);
        
        return new User($userData['id'], $userData['email'], $userData['passwordHash']);
    }
}