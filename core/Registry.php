<?php
namespace vsn\core;

final class Registry
{
	public static $_aContainer;

	public static $aMap = array();

	public static function load($sClassName, $mParameter = null)
	{
		$sAlias = $sClassName;
		if(isset(self::$aMap[$sClassName]))
			$sClassName = self::$aMap[$sClassName];
		if(class_exists($sClassName))
			$oObject = new $sClassName($mParameter);
		else
			throw new \vsn\exceptions\ClassNotFound;
		self::set($sAlias,$oObject);
		return $oObject;
	}

	public static function set($sName,$mValue)
	{
		self::$_aContainer[$sName] = $mValue;
	}
	
	public static function get($sName)
	{
		if(isset(self::$_aContainer[$sName]))
			return self::$_aContainer[$sName];
		else
			return self::load($sName);
	}
}
?>
