<?php
/* --------------------------------------------------------------
   ShopKey 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidShopKeyFormatException;

/**
 * Class ShopKey
 *
 * @package HubPublic\ValueObjects
 */
class ShopKey
{
    /**
     * Shop key
     *
     * @var string
     */
    private $key;
    
    
    /**
     * ShopKey constructor.
     *
     * @param string $shopKey Shop key string representation.
     *
     * @throws \HubPublic\Exceptions\InvalidShopKeyFormatException If the shop key format is invalid.
     */
    public function __construct(string $shopKey)
    {
        if (!$this->isValidFormat($shopKey)) {
            throw new InvalidShopKeyFormatException('The passed shop key value "' . $shopKey
                                                    . '" is in an invalid format');
        }
        $this->key = $shopKey;
    }
    
    
    /**
     * Returns the shop key.
     *
     * It is guaranteed that the key is in a valid format.
     *
     * @return string Shop key
     */
    public function asString(): string
    {
        return $this->key;
    }
    
    
    /**
     * Validates the shop key.
     *
     * @param string $shopKey Shop key string representation.
     *
     * @return bool True if the format is valid and false otherwise.
     */
    private function isValidFormat(string $shopKey): bool
    {
        $regex = '/[A-Z]{2}[0-9]{2}-[A-Z]{2}[0-9]{2}-[0-9]{2}[A-Z]{2}-[0-9]{2}[A-Z]{2}-[A-Z]{4}-[0-9]{2}[A-Z]{2}/';
        
        return preg_match($regex, $shopKey) === 1;
    }
}
