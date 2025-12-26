<?php
/* --------------------------------------------------------------
   PersonalDataSelection.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the personal data selection value object
 */
class PersonalDataSelection
{
    /**
     * Selection values
     * @var array
     */
    protected $selection = [];
    
    /**
     * Valid selection keys
     * @var array
     */
    protected $validKeys = [
        PersonalDataSelectionItem::BASE_DATA,
        PersonalDataSelectionItem::ORDERS,
        PersonalDataSelectionItem::WITHDRAWALS,
        PersonalDataSelectionItem::AGREEMENTS,
        PersonalDataSelectionItem::EMAILS,
        PersonalDataSelectionItem::CARTS,
        PersonalDataSelectionItem::REVIEWS,
        PersonalDataSelectionItem::NEWSLETTER_SUBSCRIPTIONS
    ];
    
    
    /**
     * Create an instance
     *
     * @param array $selection Selection key-value pairs
     */
    public function __construct(array $selection)
    {
        foreach ($selection as $key => $value) {
            if (!in_array($key, $this->validKeys)) {
                throw new InvalidArgumentException("Invalid key '{$key}'");
            }
            
            if (!is_bool($value)) {
                throw new InvalidArgumentException("Invalid value '{$value}' in key '{$key}'");
            }
        }
        
        $this->selection = $selection;
    }
    
    
    /**
     * Return whether the provided item is selected
     *
     * @param string $item Selection item
     *
     * @return bool Selection value
     */
    public function isSelected($item)
    {
        if (!in_array($item, $this->validKeys)) {
            throw new InvalidArgumentException("Invalid '{$item}' key");
        }
        
        return $this->selection[$item];
    }
}