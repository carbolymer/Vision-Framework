<?php
namespace vsn\core;

class Config extends \stdClass
{
	private $_aConfigs = null;
	private $_sConfigPath = null;
	private $_bIsStagable = false;
	private function __construct($sConfigParam, $bIsStagable = false)
	{
		// przydałoby sie jakieś zwracanie błędów
		if($bIsStagable)
		{
			if(is_dir($sConfigParam))
			{
				$this->_bIsStagable = true;
				$this->_sConfigPath = $sConfigParam;
				$this->_aConfigs = scandir($sConfigParam);
			}
		}else
		{
			if(file_exists($sConfigParam))
			{
				include($sConfigParam);
				foreach($vsn_config as $sKey => $aConfig)
				{
					$this->$sKey = $aConfig;
				}
			}
		}
	}

	public static function loadStagable($sConfigDir)
	{
		return new self($sConfigDir, true);
	}

	public static function load($sConfig)
	{
		return new self($sConfig);
	}

	public function loadStage($iStage)
	{
		if(!$this->_bIsStagable)
			return;
		$iStage = intval($iStage / 10);
		foreach($this->_aConfigs as $sConfigFile)
		{
			if(preg_match("#^".$iStage."(\d{1})_([\.a-z0-9]+)\.php$#si", $sConfigFile, $aMatches))
			{
				include($this->_sConfigPath.'/'.$sConfigFile);
				foreach($vsn_config as $sKey => $aConfig)
				{
					$this->$sKey = $aConfig;
				}
			}
		}
	}
}
?>
