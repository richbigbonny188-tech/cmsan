<?php
/* --------------------------------------------------------------
	PropertyList.inc.php 2015-11-17
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/


class PropertyList
{
	protected $propertyListData = array();

	public function __construct(stdClass $propertyList)
	{
		foreach($propertyList->property as $property)
		{
			if(isset($property->propertyValue->alphanumericValue))
			{
				$value = (string)$property->propertyValue->alphanumericValue->fixValue;
			}
			else if(isset($property->propertyValue->booleanValue))
			{
				$value = (bool)$property->propertyValue->booleanValue;
			}
			else if(isset($property->propertyValue->numericValue))
			{
				$value = (double)$property->propertyValue->numericValue->fixValue;
			}
			else if(isset($property->propertyValue->currencyValue))
			{
				$value = $property->propertyValue->currencyValue->fixValue->value;
				$value .= ' '.$property->propertyValue->currencyValue->fixValue->currency;
			}
			else
			{
				die('unhandled value type '.print_r($property, true));
			}
			$this->propertyListData[(string)$property->name] = $value;
		}
	}

	public function __isset($name)
	{
		return isset($this->propertyListData[$name]);
	}

	public function __get($name)
	{
		if(isset($this->propertyListData[$name]))
		{
			return $this->propertyListData[$name];
		}
		else
		{
			return null;
		}
	}

	public function getAll()
	{
		return $this->propertyListData;
	}
}
