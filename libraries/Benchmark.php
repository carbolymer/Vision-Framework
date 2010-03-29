<?php
namespace vsn\libraries;
abstract class Benchmark
{
	const PRECISION = 7;
	
	private static $_aContents = array();
	
	private static function _getMicrotime()
	{
		return microtime(true);
	}
	private static function _getMemoryUsage()
	{
		return memory_get_usage();
	}

	public static function mark($sName)
	{
		self::$_aContents[$sName]['starttime'] = self::_getMicrotime();
		self::$_aContents[$sName]['startmemory'] = self::_getMemoryUsage();
		self::$_aContents[$sName]['endtime'] = null;
		self::$_aContents[$sName]['endmemory'] = null;
	}

	public static function stop($sName)
	{
		self::$_aContents[$sName]['endtime'] = self::_getMicrotime() - self::$_aContents[$sName]['starttime'];
		self::$_aContents[$sName]['endmemory'] = self::_getMemoryUsage() - self::$_aContents[$sName]['startmemory'];
		return self::$_aContents[$sName];
	}

	public static function getTime($sEvent1, $sEvent2)
	{
		return self::$_aContents[$sEvent2]['starttime'] - self::$_aContents[$sEvent1]['starttime'];
	}

	public static function getMemory($sEvent1, $sEvent2)
	{
		return self::$_aContents[$sEvent2]['startmemory'] - self::$_aContents[$sEvent1]['startmemory'];
	}

	public static function getAll()
	{
		return self::$_aContents;
	}
}
?>
