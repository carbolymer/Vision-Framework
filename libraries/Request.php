<?php
namespace \vsn\libraries;
final class Request
{
	const INTEGER = 1;
	const FLOAT = 2;
	const STRING = 3;
	const ALPHANUM = 4;
	const WORD = 5;
	const NO_TYPE = 6;
	
	private static $sRequestMethod = null;
	private static $aRequest = array();

	static public function init()
	{
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': 
				self::$aRequest = $_GET; 
				break;
			case 'POST':
				self::$aRequest = $_POST; 
				break;
			default:
				trigger_error('Request method '.$_SERVER['REQUEST_METHOD'].' not supported.');
				
		}
	}
	
	//pobierz zmienna, przy okazji ja walidujac
	static public function get($sName,$iType = self::NO_TYPE)
	{
		switch($iType)
		{
			case self::INTEGER:
				return intval(self::$aRequest[$sName]);
				break;
			case self::FLOAT:
				return floatval(self::$aRequest[$sName]);
				break;
			case self::STRING:
				return strval(self::$aRequest[$sName]);
				break;
			case self::ALPHANUM:
				return preg_replace("#([^a-z0-9_\s]+|[\n\t\r]+)#i",'',self::$aRequest[$sName]);
				break;
			case self::WORD:
				return preg_replace("#([^a-z\s]+)#i",'',self::$aRequest[$sName]);
				break;
			default:
				return (isset(self::$aRequest[$sName])?self::$aRequest[$sName]:null);
				break;
				
		}
	}
	
	//zwraca przeslany plik
	static public function getFile($sName)
	{
		return @$_FILES[$sName];
	}
}
?>
