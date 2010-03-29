<?php
namespace vsn\libraries;

class LogWrapper
{
	private $aConfig = null;
	private $sMode = null;
	private $sPath = null;
	private $sFile = null;

	public static function register()
	{
		stream_wrapper_register('log', __CLASS__);
	}

	public function __construct()
	{
		$this->aConfig = \vsn\core\Registry::get('config');
		$this->sPath = $this->aConfig->core['application_path'].'/'.$this->aConfig->LogWrapper['log_folder'];
	}

	public function stream_open($sPath, $sMode, $sOptions, &$sOpenedPath)
	{
		$aPath = parse_url($sPath);
		
		$this->sFile = $this->sPath.'/'.str_replace($aPath['scheme'].'://','',$sPath);
		fclose(fopen($this->sFile, $sMode));
		
		return true;
	}

	public function stream_write($sData)
	{
		$sAppTime = '['.(microtime(true)-START_TIME).'] ';
		return (file_put_contents($this->sFile, $sAppTime.$sData."\n", FILE_APPEND)-strlen($sAppTime)-1);
	}
	
	public function stream_read()
	{
		return file_get_contents($this->sPath);
	}
}
?>
