<?php
/* --------------------------------------------------------------
   ExtendedSimpleXMLElement.inc.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ExtendedSimpleXMLElement
 *
 * @category System
 * @package  Shared
 */
class ExtendedSimpleXMLElement extends SimpleXMLElement
{
    /**
     * @param string $name
     * @param null   $value
     * @param null   $ns
     *
     * @return \SimpleXMLElement
     */
    public function addChild($name, $value = null, $ns = null): SimpleXMLElement
    {
        if ($value !== null && preg_match('/[äöüß&<>\"\']/', $value) === 1) {
            $fullNodeName = ($ns !== null ? $ns . ':' : '') . $name;
            $child        = $this->addChild($fullNodeName);
            $node         = dom_import_simplexml($child);
            $node->appendChild($node->ownerDocument->createCDATASection($value));
            
            return $child;
        }
        
        return parent::addChild($name, $value, $ns);
    }
}