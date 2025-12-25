<?php

namespace Gambio\Shop\SellingUnit\Unit\Builders;

use Gambio\Shop\SellingUnit\Unit\Builders\Interfaces\ModelBuilderInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Model;

class ModelBuilder implements ModelBuilderInterface
{
    protected $modelParts = [];
    
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
     * @inheritDoc
     */
    public function wipeData(): ModelBuilderInterface
    {
        $this->modelParts = [];
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function withModelAtPos(string $model, int $pos): ModelBuilderInterface
    {
        $this->modelParts[$pos] = $model;
        
        return $this;
    }
    
    
    public function withShowInShoppingCartAndWishlist(bool $show): ModelBuilderInterface
    {
        $this->showInShoppingCartAndWishlist = $show;
        
        return $this;
    }
    
    
    public function withShowInProductLists(bool $show): ModelBuilderInterface
    {
        $this->showInProductLists = $show;
        
        return $this;
    }
    
    
    public function withShowInProductDetails(bool $show): ModelBuilderInterface
    {
        $this->showInProductDetails = $show;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function build(): Model
    {
        $data = [];
    
        if ($this->modelParts) {
            ksort($this->modelParts);
            $data = $this->modelParts;
            $this->modelParts = [];
        }
        
        return new Model(
            implode('-', $data),
            $this->showInShoppingCartAndWishlist,
            $this->showInProductLists,
            $this->showInProductDetails
        );
    }
}