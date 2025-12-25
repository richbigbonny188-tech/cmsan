<?php
/* --------------------------------------------------------------
   AfterbuyGlobal.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\Model;

/**
 * Class AfterbuyGlobal
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model\Request
 */
class AfterbuyGlobal implements XmlSerializable
{
    /**
     * @var string
     */
    private string $partnerToken;
    
    
    /**
     * @var string
     */
    private string $accountToken;
    
    
    /**
     * @var string
     */
    private string $callName;
    
    
    /**
     * AfterbuyGlobal constructor.
     *
     * @param string $partnerToken
     * @param string $accountToken
     * @param string $callName
     */
    public function __construct(string $partnerToken, string $accountToken, string $callName)
    {
        $this->partnerToken = $partnerToken;
        $this->accountToken = $accountToken;
        $this->callName     = $callName;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toXmlString(): string
    {
        $indent = $this->indent();
        
        return <<<XML
$indent<AfterbuyGlobal>
$indent    <PartnerToken><![CDATA[$this->partnerToken]]></PartnerToken>
$indent    <AccountToken><![CDATA[$this->accountToken]]></AccountToken>
$indent    <CallName><![CDATA[$this->callName]]></CallName>
$indent</AfterbuyGlobal>
XML;
    }
    
    
    /**
     * @inheritDoc
     */
    public function indent(): string
    {
        return str_repeat(' ', 4);
    }
}