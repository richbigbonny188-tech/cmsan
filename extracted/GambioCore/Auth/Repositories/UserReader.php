<?php
/* --------------------------------------------------------------
   UserReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Core\Auth\Exceptions\UserNotFoundException;

/**
 * Class UserReader
 *
 * @package Gambio\Core\Auth\Repositories
 */
class UserReader
{
    /**
     * @var Connection
     */
    private $db;
    
    
    /**
     * UserReader constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Returns user data of user with provided email.
     *
     * @param string $email
     *
     * @return array
     *
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function getUserByEmail(string $email): array
    {
        $userData = $this->db->createQueryBuilder()
            ->select('customers_id', 'customers_email_address', 'customers_password')
            ->from('customers')
            ->where('customers_email_address = :email')
            ->setParameter('email', $email)
            ->executeQuery()
            ->fetchAssociative();
        
        if ($userData === false) {
            throw UserNotFoundException::forEmail($email);
        }
        
        return [
            'id'           => (int)$userData['customers_id'],
            'email'        => $userData['customers_email_address'],
            'passwordHash' => $userData['customers_password'],
        ];
    }
}