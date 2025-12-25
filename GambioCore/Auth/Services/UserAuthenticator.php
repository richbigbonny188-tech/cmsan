<?php
/* --------------------------------------------------------------
   UserAuthenticator.php 2020-04-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Services;

use Gambio\Core\Auth\Exceptions\AuthenticationException;
use Gambio\Core\Auth\Exceptions\UserNotFoundException;
use Gambio\Core\Auth\HashStrategy;
use Gambio\Core\Auth\Repositories\UserRepository;
use Gambio\Core\Auth\UserId;

/**
 * Class UserAuthenticator
 *
 * @package Gambio\Core\Auth
 */
class UserAuthenticator implements \Gambio\Core\Auth\UserAuthenticator
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    
    /**
     * @var HashStrategy[]
     */
    private $hashStrategies;
    
    
    /**
     * WebAuthenticator constructor.
     *
     * @param UserRepository $userRepository
     * @param HashStrategy   ...$hashStrategies
     */
    public function __construct(UserRepository $userRepository, HashStrategy ...$hashStrategies)
    {
        $this->userRepository = $userRepository;
        $this->hashStrategies = $hashStrategies;
    }
    
    
    /**
     * @inheritDoc
     */
    public function authenticate(string $email, string $password): UserId
    {
        try {
            $user = $this->userRepository->getUserByEmail($email);
            foreach ($this->hashStrategies as $hashStrategy) {
                if ($hashStrategy->verify($password, $user->passwordHash())) {
                    return \Gambio\Core\Auth\Model\UserId::create($user->id());
                }
            }
            
            throw new AuthenticationException('Can not authenticate user.');
        } catch (UserNotFoundException $exception) {
            throw new AuthenticationException('Can not authenticate user.');
        }
    }
}