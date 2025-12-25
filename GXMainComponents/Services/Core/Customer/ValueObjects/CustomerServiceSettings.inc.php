<?php
/* --------------------------------------------------------------
   CustomerServiceSettings.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerServiceSettingsInterface');

/**
 * Value Object
 *
 * Class CustomerServiceSettings
 *
 * Represents the default settings of a customer/guest
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerServiceSettingsInterface
 */
class CustomerServiceSettings implements CustomerServiceSettingsInterface
{
    /**
     * @var int
     */
    protected $defaultCustomerStatusId;
    
    /**
     * @var int
     */
    protected $defaultGuestStatusId;
    
    /**
     * @var bool
     */
    protected $moveOnlyIfNoGuest;
    
    
    /**
     * Constructor of the class CustomerServiceSettings.
     *
     * @param \IdType   $defaultCustomerStatusId Default customer status id.
     * @param \IdType   $defaultGuestStatusId    Default guest status id.
     * @param \BoolType $moveOnlyIfNoGuest       Move only if no guest flag.
     */
    public function __construct(
        IdType $defaultCustomerStatusId,
        IdType $defaultGuestStatusId,
        BoolType $moveOnlyIfNoGuest
    ) {
        $this->defaultCustomerStatusId = $defaultCustomerStatusId->asInt();
        $this->defaultGuestStatusId    = $defaultGuestStatusId->asInt();
        $this->moveOnlyIfNoGuest       = $moveOnlyIfNoGuest->asBool();
    }
    
    
    /**
     * Returns the default customer status ID.
     *
     * @return int
     */
    public function getDefaultCustomerStatusId()
    {
        return $this->defaultCustomerStatusId;
    }
    
    
    /**
     * Returns the default guest customer status ID.
     *
     * @return int
     */
    public function getDefaultGuestStatusId()
    {
        return $this->defaultGuestStatusId;
    }
    
    
    /**
     * Returns the only no guests should be moved.
     *
     * @return bool
     */
    public function getMoveOnlyIfNoGuest()
    {
        return $this->moveOnlyIfNoGuest;
    }
}
 