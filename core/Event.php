<?php
namespace vsn\core;

final class Event {

	public static $_aEvents = array();

	private static $_aHasRun = array();

	public static function init($aConfig)
	{
		if(!empty($aConfig))
			foreach($aConfig as $sEvent => $aCallbacks)
			{
				if(!@is_array(self::$_aEvents[$sEvent]))
					self::$_aEvents[$sEvent] = array();
				self::$_aEvents[$sEvent] = array_merge(self::$_aEvents[$sEvent],$aCallbacks);
			}
	}

	public static function add($sName, $aCallback)
	{
		if(!isset(self::$_aEvents[$sName]))
			self::$_aEvents[$sName] = array();
		//var_dump(self::$_aEvents[$sName]);
		self::$_aEvents[$sName][] = $aCallback;
		//var_dump(self::$_aEvents[$sName]);

		return true;
	}

	public static function get($sName)
	{
		return empty(self::$_aEvents[$sName]) ? array() : self::$_aEvents[$sName];
	}

	public static function run($sName, $aParameters = array(), $bAutoremove = false)
	{
		if (!empty(self::$_aEvents[$sName]))
		{
			if(!is_array($aParameters))
				$aParameters = array($aParameters);
			$aCallbacks = &self::$_aEvents[$sName];
			reset($aCallbacks);
			while(true)
			{
				if($bAutoremove == true && !empty($aCallbacks))
				{
					$mCallback = array_shift($aCallbacks);
					if($mCallback === null)
						break;
				}
				else
				{
					$mCallback = current($aCallbacks);
					next($aCallbacks);
				}
				if($mCallback === false)
					break;

				try
				{
					call_user_func_array($mCallback,$aParameters);
				}
				catch(HaltCallbackExecutionException $e)
				{
					continue;
				}
				catch(HaltEventExecutionException $e)
				{
					break;
				}
			}
		}
		self::$_aHasRun[$sName] = $sName;
	}

	public static function has_run($sName)
	{
		return isset(self::$_aHasRun[$sName]);
	}
}
?>
