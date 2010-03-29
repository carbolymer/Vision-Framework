<?php
namespace vsn\core;

final class Loader
{
	public static function load($sClassName)
	{
		if(stristr($sClassName, '\\') !== false)
		{// namespaces ._.
			if(strpos($sClassName,'\\') === 0)
				$sClassName = ltrim($sClassName,'\\');
			$aConfig = Registry::get('config')->core;
			$aClassName = explode('\\',$sClassName);
			switch($aClassName[0])
			{
				case 'vsn':
					$sPath = $aConfig['framework_path'];
					break;
				case 'app':
				default:
					$sPath = $aConfig['application_path'];
			}
			array_shift($aClassName);
			$sPath .= '/'.implode('/',$aClassName);
			if(file_exists($sPath.'.php'))
			{
				require_once $sPath.'.php';
				return true;
			}
		}

		return false;
	}
}
?>
