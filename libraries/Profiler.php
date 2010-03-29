<?php
namespace vsn\libraries;
abstract class Profiler
{
	const PRECISION = 7;
	
	private static $aContents = array();
	
	private static $aPath = array();
	
	private static function _getMicrotime()
	{
		return microtime(true);
	}
	
	private static function &_getCurrent()
	{
		$aCurrent = &self::$aContents;
		if(empty(self::$aPath))
			return $aCurrent;
		foreach(self::$aPath as $sKey)
			$aCurrent =& $aCurrent['child'][$sKey];
		return $aCurrent;
	}

	public static function start($sName)
	{
		$aCurrent =& self::_getCurrent();
		$aCurrent['child'][$sName] = array('time' => self::_getMicrotime(), 'child' => null);
		self::$aPath[] = $sName;
	}

	public static function stop($sName)
	{
		if(empty(self::$aPath))
			throw new \Exception('[Profiler] There is no running profiler session.');
		if(end(self::$aPath) != $sName)
			throw new \Exception ('[Profiler] Firstly stop session: <i>'.end(self::$aPath).'</i>, not '.$sName);
		$aCurrent =& self::_getCurrent();
		$aCurrent['time'] = round(self::_getMicrotime() - $aCurrent['time'],self::PRECISION);
		array_pop(self::$aPath);
		return $aCurrent['time'];
	}

	public static function getAllTimestamps()
	{
		return self::$aContents['child'];
	}
}
?>
