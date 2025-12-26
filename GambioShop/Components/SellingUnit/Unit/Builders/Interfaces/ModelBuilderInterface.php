<?php

namespace Gambio\Shop\SellingUnit\Unit\Builders\Interfaces;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\Model;

/**
 * Interface ModelBuilderInterface
 *
 * @package Gambio\Shop\SellingUnit\Unit\Builders\Interfaces
 */
interface ModelBuilderInterface
{
    /**
     * @return ModelBuilderInterface
     */
    public function wipeData(): ModelBuilderInterface;
    
    
    /**
     * @param string $model
     * @param int    $pos
     *
     * @return ModelBuilderInterface
     */
    public function withModelAtPos(string $model, int $pos): ModelBuilderInterface;
    
    
    /**
     * @param bool $show
     *
     * @return ModelBuilderInterface
     */
    public function withShowInShoppingCartAndWishlist(bool $show): ModelBuilderInterface;
    
    
    /**
     * @param bool $show
     *
     * @return ModelBuilderInterface
     */
    public function withShowInProductLists(bool $show): ModelBuilderInterface;
    
    
    /**
     * @param bool $show
     *
     * @return ModelBuilderInterface
     */
    public function withShowInProductDetails(bool $show): ModelBuilderInterface;
    
    
    /**
     * @return Model
     */
    public function build(): Model;
    
}