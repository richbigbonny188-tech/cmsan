<?php
/* --------------------------------------------------------------
   DeleteHistoryScope.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DeleteHistoryScope
 */
class DeleteHistoryScope
{
    /**
     * Scope constants
     */
    const SCOPE_ORDERS     = 'delete_scope_orders';
    const SCOPE_PRODUCTS   = 'delete_scope_products';
    const SCOPE_CATEGORIES = 'delete_scope_categories';
    const SCOPE_CUSTOMERS  = 'delete_scope_customers';
    
    /**
     * @var string
     */
    protected $scope;
    
    /**
     * @var array
     */
    protected static $allowedScopes = [
        self::SCOPE_ORDERS,
        self::SCOPE_PRODUCTS,
        self::SCOPE_CATEGORIES,
        self::SCOPE_CUSTOMERS
    ];
    
    
    /**
     * DeleteHistoryScope constructor.
     *
     * @param string $scope Delete history scope.
     */
    public function __construct($scope)
    {
        if (!in_array($scope, static::$allowedScopes)) {
            throw new InvalidArgumentException('Invalid scope "' . $scope . '" provided! Valid scopes are "'
                                               . implode('", "', static::$allowedScopes) . '".');
        }
        $this->scope = $scope;
    }
    
    
    /**
     * Creates a new instance of a delete history orders scope.
     *
     * @return \DeleteHistoryScope Orders scope.
     */
    public static function orders()
    {
        return MainFactory::create(static::class, static::SCOPE_ORDERS);
    }
    
    
    /**
     * Creates a new instance of a delete history products scope.
     *
     * @return \DeleteHistoryScope Products scope.
     */
    public static function products()
    {
        return MainFactory::create(static::class, static::SCOPE_PRODUCTS);
    }
    
    
    /**
     * Creates a new instance of a delete history categories scope.
     *
     * @return \DeleteHistoryScope Categories scope.
     */
    public static function categories()
    {
        return MainFactory::create(static::class, static::SCOPE_CATEGORIES);
    }
    
    
    /**
     * Creates a new instance of a delete history customers scope.
     *
     * @return \DeleteHistoryScope Customers scope.
     */
    public static function customers()
    {
        return MainFactory::create(static::class, static::SCOPE_CUSTOMERS);
    }
    
    
    /**
     * Returns the current scope.
     *
     * @return string
     */
    public function scope()
    {
        return $this->scope;
    }
}