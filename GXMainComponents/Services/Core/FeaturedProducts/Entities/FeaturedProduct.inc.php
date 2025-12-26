<?php
/* --------------------------------------------------------------
   FeaturedProduct.inc.php 2019-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FeaturedProduct
 */
class FeaturedProduct implements FeaturedProductInterface
{
    /**
     * @var FeaturedProductSettingsInterface
     */
    protected $settings;
    
    /**
     * Product ID
     *
     * @var int
     */
    protected $productsId;
    
    /**
     * Product name.
     *
     * @var string
     */
    protected $name;
    
    /**
     * VPE ID
     *
     * @var int
     */
    protected $vpeID;
    
    /**
     * Image
     *
     * @var string
     */
    protected $image;
    
    /**
     * Image alternative text
     *
     * @var string
     */
    protected $imageAltText;
    
    /**
     * Product short description
     *
     * @var string
     */
    protected $shortDescription;
    
    /**
     * Product meta description
     *
     * @var string
     */
    protected $metaDescription;
    
    /**
     * Shipping status name.
     *
     * @var string
     */
    protected $shippingStatusName;

    /**
     * Product price.
     *
     * @var float
     */
    protected $price;

    /**
     * Product tax class ID.
     *
     * @var float
     */
    protected $taxClassId;

    /**
     * Product quantity.
     *
     * @var float
     */
    protected $quantity;
    
    
    /**
     * FeaturedProduct constructor.
     *
     * @param FeaturedProductSettingsInterface $settings
     * @param IntType                          $productsId
     * @param StringType                       $name
     * @param IntType                          $vpeID
     * @param StringType                       $image
     * @param StringType                       $imageAltText
     * @param StringType                       $shortDescription
     * @param StringType                       $metaDescription
     * @param StringType                       $shippingStatusName
     * @param DecimalType                      $price
     * @param IntType                          $taxClassId
     * @param DecimalType                      $quantity
     */
    public function __construct(
        FeaturedProductSettingsInterface $settings,
        IntType $productsId,
        StringType $name,
        IntType $vpeID,
        StringType $image,
        StringType $imageAltText,
        StringType $shortDescription,
        StringType $metaDescription,
        StringType $shippingStatusName,
        DecimalType $price,
        IntType $taxClassId,
        DecimalType $quantity
    ) {
        $this->settings           = $settings;
        $this->productsId         = $productsId;
        $this->name               = $name;
        $this->vpeID              = $vpeID;
        $this->image              = $image;
        $this->imageAltText       = $imageAltText;
        $this->shortDescription   = $shortDescription;
        $this->metaDescription    = $metaDescription;
        $this->shippingStatusName = $shippingStatusName;
        $this->price              = $price;
        $this->taxClassId         = $taxClassId;
        $this->quantity           = $quantity;
    }
    
    
    /**
     * Return the featured product settings
     *
     * @return FeaturedProductSettingsInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    
    /**
     * Returns the product ID.
     *
     * @return int
     */
    public function getProductsId(): int
    {
        return $this->productsId->asInt();
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name->asString();
    }
    
    
    /**
     * Get VPE ID
     *
     * @return int VPE ID.
     */
    public function getVpeID()
    {
        return $this->vpeID->asInt();
    }
    
    
    /**
     * Returns the image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image->asString();
    }
    
    
    /**
     * Returns the alternative image text.
     *
     * @return string
     */
    public function getImageAltText()
    {
        return $this->imageAltText->asString();
    }
    
    
    /**
     * Returns product short description.
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription->asString();
    }
    
    
    /**
     * Returns product meta description.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription->asString();
    }
    
    
    /**
     * Returns shipping status name.
     *
     * @return string
     */
    public function getShippingStatusName()
    {
        return $this->shippingStatusName->asString();
    }


    /**
     * Returns product price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price->asDecimal();
    }


    /**
     * Returns product tax class ID.
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->taxClassId->asInt();
    }


    /**
     * Returns product tax class ID.
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity->asDecimal();
    }
}