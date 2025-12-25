<?php
/*--------------------------------------------------------------------
 Model.php 2022-04-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class Model
 *
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class Model
{
    /**
     * @var string
     */
    protected $model;
    
    /**
     * @var bool
     */
    private $showInShoppingCartAndWishlist;
    
    /**
     * @var bool
     */
    private $showInProductLists;
    
    /**
     * @var bool
     */
    private $showInProductDetails;
    
    
    /**
     * Model constructor.
     *
     * @param string $model
     */
    public function __construct(
        string $model,
        bool   $showInShoppingCartAndWishlist,
        bool   $showInProductLists,
        bool   $showInProductDetails
    ) {
        $this->model                         = $model;
        $this->showInShoppingCartAndWishlist = $showInShoppingCartAndWishlist;
        $this->showInProductLists            = $showInProductLists;
        $this->showInProductDetails          = $showInProductDetails;
    }
    
    
    /**
     * @return bool
     */
    public function showInShoppingCartAndWishlist(): bool
    {
        return $this->showInShoppingCartAndWishlist;
    }
    
    
    /**
     * @return bool
     */
    public function showInProductLists(): bool
    {
        return $this->showInProductLists;
    }
    
    
    /**
     * @return bool
     */
    public function showInProductDetails(): bool
    {
        return $this->showInProductDetails;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->model;
    }
}