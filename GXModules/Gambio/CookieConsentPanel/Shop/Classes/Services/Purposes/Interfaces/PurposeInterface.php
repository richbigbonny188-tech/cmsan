<?php
/* --------------------------------------------------------------
  PurposeInterface.php 2020-01-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\CookieConsentPanel\Services\Purposes\Interfaces;

/**
 * Interface PurposeInterface
 * @package Gambio\CookieConsentPanel\Services\Purposes\Interfaces
 */
interface PurposeInterface
{
    /**
     * @return CategoryInterface
     */
    public function category(): CategoryInterface;
    
    /**
     * @return DescriptionInterface
     */
    public function description(): DescriptionInterface;
    
    /**
     * @return NameInterface
     */
    public function name(): NameInterface;
    
    /**
     * @return StatusInterface
     */
    public function status(): StatusInterface;
    
    /**
     * @return DeletableInterface
     */
    public function deletable(): DeletableInterface;
    
    /**
     * @return AliasInterface
     */
    public function alias(): AliasInterface;
    
    /**
     * @return IdInterface
     */
    public function id(): IdInterface;
}