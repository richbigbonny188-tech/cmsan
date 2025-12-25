<?php

/* --------------------------------------------------------------
   OrderStatusInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Entities
 */
interface OrderStatusInterface
{
    /**
     * Returns the order status id.
     *
     * @return int|null
     */
    public function getId();
    
    
    /**
     * Returns the order status names.
     *
     * @return array Order status names.
     */
    public function getNames();
    
    
    /**
     * Returns the order status name by the given language id.
     *
     * @param LanguageCode $languageCode Expected language code.
     *
     * @return string Expected status name by given language id.
     * @throws Exception If no name exists by the given language code.
     */
    public function getName(LanguageCode $languageCode);
    
    
    /**
     * Sets the order status name.
     *
     * @param \LanguageCode $languageCode Language code of order status name.
     * @param \StringType   $name         Order status name.
     *
     * @return $this|OrderStatus Same instance for chained method calls.
     */
    public function setName(LanguageCode $languageCode, StringType $name);
    
    
    /**
     * Returns the order status label-color.
     *
     * @return string Color of order status label.
     */
    public function getColor();
    
    
    /**
     * Sets the order status label-color.
     *
     * @param StringType $color New color of order status label.
     *
     * @return $this|OrderStatus Same instance for chained method calls.
     */
    public function setColor(StringType $color);
}