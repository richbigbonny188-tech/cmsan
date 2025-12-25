<?php
/* --------------------------------------------------------------
   ThemeJavaScriptOverloadController.php 2019-04-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ThemeJavaScriptOverloadController
{
	/**
	 * @var \ExistingDirectory
	 */
	protected $variantDirectory;
	
	/**
	 * @var array
	 */
	protected $selectedVariants = [];
	
	/**
	 * @var array
	 */
	protected $variantScripts   = [];
	
	/**
	 * ThemeJavaScriptOverloadController constructor.
	 *
	 * @param \ExistingDirectory $variants
	 */
	public function __construct(ExistingDirectory $variants)
	{
		$this->variantDirectory = $variants;
		$this->mapVariants();
	}
	
	
	/**
	 * @param string $variant
	 * @param string $value
	 */
	public function setVariant(string $variant, string $value) : void
	{
		$this->selectedVariants[$variant] = $value;
	}
	
	/**
	 * @param string $html
	 *
	 * @return string
	 */
	public function overloadJavaScripts(string $html) : string
	{
		$scriptTags = $this->getScriptTags($html);
		
		if(count($scriptTags) !== 0)
		{
			foreach($scriptTags as $scriptTag)
			{
				$srcAttribute = $this->getSrcAttribute($scriptTag);
				
				if($srcAttribute !== false)
				{
					[$overloaded, $newSrc] = $this->overloadJavaScript($srcAttribute);
					
					if($overloaded)
					{
						$replace = str_replace($srcAttribute, $newSrc, $scriptTag);
						$html    = str_replace($scriptTag, $replace, $html);
					}
				}
			}
		}
		
		return $html;
	}
	
	
	/**
	 * @var string
	 */
	protected const IS_THEME_JAVASCRIPT_PATTERN = '/public\/theme\//';
	
	
	/**
	 * @param string $srcAttribute
	 *
	 * @return array (bool|string)
	 */
	protected function overloadJavaScript(string $srcAttribute) : ?array
	{
		if(preg_match(self::IS_THEME_JAVASCRIPT_PATTERN, $srcAttribute) !== 0)
		{
			$selectedVariantScripts = $this->getSelectedVariantScripts();
			
			if(array_key_exists($srcAttribute, $selectedVariantScripts))
			{
				return [true, $selectedVariantScripts[$srcAttribute]];
			}
		}
		
		return [false, $srcAttribute];
	}
	
	
	/**
	 * @var string
	 */
	protected const PUBLIC_THEME_PATH_PATTERN = '#' . '^.*(public\/theme)' . '#';
	
	/**
	 * @return array
	 */
	public function getSelectedVariantScripts() : array
	{
		$result = [];
		
		foreach($this->selectedVariants as $variant => $selectedVariant)
		{
			if(is_array($this->variantScripts[$variant]) && array_key_exists($selectedVariant, $this->variantScripts[$variant])
			&& is_array($this->variantScripts[$variant][$selectedVariant])
		    && count($this->variantScripts[$variant][$selectedVariant]))
			{
				foreach($this->variantScripts[$variant][$selectedVariant] as $script)
				{
					$script                = $originalPath = preg_replace(self::PUBLIC_THEME_PATH_PATTERN, '$1', $script);
					
					$obsoleteVariantPath   = 'variants' . DIRECTORY_SEPARATOR . $variant . DIRECTORY_SEPARATOR
					                       . $selectedVariant . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR
					                       . 'javascripts';
					
					$originalScriptPath    = 'javascripts' . DIRECTORY_SEPARATOR . 'system';
					$originalPath          = str_replace($obsoleteVariantPath, $originalScriptPath, $originalPath);
					
					$result[$originalPath] = $script;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * @var string
	 */
	protected const SCRIPT_TAG_PATTERN = '/<script.*src.*>\s*<\/script>/';
	
	
	/**
	 * @param string $html
	 *
	 * @return string[] every script tag in $html with src and an empty body
	 */
	protected function getScriptTags(string $html) : array
	{
		if(preg_match_all(self::SCRIPT_TAG_PATTERN, $html, $result))
		{
			return array_shift($result);
		}
		
		return [];
	}
	
	
	/**
	 * @var string
	 */
	protected const SRC_ATTRIBUTE_PATTERN = '/src\s*\=\s*[\'|"]([^\'"]+)[\'|"]/';
	
	/**
	 * @param string $scriptTag
	 *
	 * @return mixed src attribute of $scriptTag
	 */
	protected function getSrcAttribute(string $scriptTag)
	{
		if(preg_match(self::SRC_ATTRIBUTE_PATTERN, $scriptTag, $result))
		{
			return array_pop($result);
		}
	
		return false;
	}
	
	/*
	 *
	 */
	protected function mapVariants() : void
	{
		foreach(new DirectoryIterator($this->variantDirectory->getDirPath()) as $variant)
		{
			if(!$variant->isDot() && $variant->isDir())
			{
				$variantTypeName = $variant->getBasename();
				$variantTypePath = $variant->getPath() . DIRECTORY_SEPARATOR . $variantTypeName;
				
				if(is_dir($variantTypePath))
				{
					foreach(new DirectoryIterator($variantTypePath) as $variantType)
					{
						if(!$variantType->isDot() && $variantType->isDir())
						{
							$variantName = $variantType->getBasename();
							$variantPath = $variantTypePath . DIRECTORY_SEPARATOR . $variantName;
							$scriptPath  = $variantPath . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'javascripts';
							
							if(is_dir($scriptPath))
							{
								foreach(new DirectoryIterator($scriptPath) as $jsFile)
								{
									if(!$jsFile->isDot() && $jsFile->isFile())
									{
										$this->variantScripts[$variantTypeName][$variantName][] = $scriptPath . DIRECTORY_SEPARATOR . $jsFile->getBasename();
									}
								}
							}
						}
					}
				}
			}
		}
	}
}