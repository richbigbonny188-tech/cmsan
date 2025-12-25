<?php
/* --------------------------------------------------------------
   ShoppingCartServiceFactory.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ShoppingCartServiceFactory
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class ShoppingCartServiceFactory implements ShoppingCartServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * SharedShoppingCartServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    public function createShoppingCartService()
    {
        return MainFactory::create('MinimalShoppingCartService', $this->db);
    }
    
    
    /**
     * Creates a SharedShoppingCartService instance.
     *
     * @return bool|SharedShoppingCartService
     */
    public function createSharedShoppingCartService()
    {
        return MainFactory::create('SharedShoppingCartService',
                                   $this->createSharedShoppingCartRepository(),
                                   $this->createSharedShoppingCartSettings());
    }
    
    
    /**
     * Creates a SharedShoppingCartRepository instance.
     *
     * @return bool|SharedShoppingCartRepository
     */
    protected function createSharedShoppingCartRepository()
    {
        return MainFactory::create('SharedShoppingCartRepository',
                                   $this->createSharedShoppingCartReader(),
                                   $this->createSharedShoppingCartWriter(),
                                   $this->createSharedShoppingCartDeleter());
    }
    
    
    /**
     * Creates a SharedShoppingCartReader instance.
     *
     * @return bool|SharedShoppingCartReader
     */
    protected function createSharedShoppingCartReader()
    {
        return MainFactory::create('SharedShoppingCartReader', $this->db);
    }
    
    
    /**
     * Creates a SharedShoppingCartWriter instance.
     *
     * @return bool|SharedShoppingCartWriter
     */
    protected function createSharedShoppingCartWriter()
    {
        return MainFactory::create('SharedShoppingCartWriter', $this->db);
    }
    
    
    /**
     * Creates a SharedShoppingCartDeleter instance.
     *
     * @return bool|SharedShoppingCartDeleter
     */
    protected function createSharedShoppingCartDeleter()
    {
        return MainFactory::create('SharedShoppingCartDeleter', $this->db);
    }
    
    
    /**
     * Creates a SharedShoppingCartSettings instance.
     *
     * @return bool|SharedShoppingCartSettings
     */
    protected function createSharedShoppingCartSettings()
    {
        return MainFactory::create('SharedShoppingCartSettings');
    }
}