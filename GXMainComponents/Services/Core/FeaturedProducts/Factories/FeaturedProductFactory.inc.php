<?php
/* --------------------------------------------------------------
   FeaturedProductFactory.inc.php 2019-09-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FeaturedProductFactory
 */
class FeaturedProductFactory
{
    /**
     * Create new featured product entity object
     *
     * @param FeaturedProductSettingsInterface $settings           Product settings
     * @param IntType                          $id                 Product ID
     * @param StringType                       $name               Product name
     * @param IntType                          $vpeId              Product VPE ID
     * @param StringType                       $image              Product image
     * @param StringType                       $imageAltText       Product image alternative text
     * @param StringType                       $shortDescription   Product short description
     * @param StringType                       $metaDescription    Product meta description
     * @param StringType                       $shippingStatusName Product shipping status name
     * @param DecimalType                      $price              Product price
     * @param IntType                          $taxClassId         Product tax class ID
     * @param DecimalType                      $quantity           Product quantity
     *
     * @return FeaturedProduct
     */
    public static function create(
        FeaturedProductSettingsInterface $settings,
        IntType $id,
        StringType $name,
        IntType $vpeId,
        StringType $image,
        StringType $imageAltText,
        StringType $shortDescription,
        StringType $metaDescription,
        StringType $shippingStatusName,
        DecimalType $price,
        IntType $taxClassId,
        DecimalType $quantity
    ) {
        return new FeaturedProduct($settings,
                                   $id,
                                   $name,
                                   $vpeId,
                                   $image,
                                   $imageAltText,
                                   $shortDescription,
                                   $metaDescription,
                                   $shippingStatusName,
                                   $price,
                                   $taxClassId,
                                   $quantity);
    }
}