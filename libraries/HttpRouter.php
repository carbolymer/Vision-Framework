<?php
namespace vsn\libraries;

final class HttpRouter extends AbstractRouter
{
/*TOFIX
	public static function url($sController='',$sAction='',$aParameters=array())
	{
		$sUrl = 'http://'.Config::get('domain').Config::get('urlBase').urlencode($sController);
		if($sAction != '') $sUrl .= '/'.urlencode($sAction);
		//if(!empty($aParameters)) $sUrl .= '?'.http_build_query($aParameters,'q');
		if(!empty($aParameters))
			foreach($aParameters as $sKey=>$mVal)
				if(is_int($sKey))
					$sUrl .= '/'.urlencode($mVal);
				else
					$sUrl .= '/'.urlencode($sKey).':'.urlencode($mVal);
				
		return $sUrl;
	}
	
	//wygenerowanie urla z subdomeny
	public static function domainUrl($sSubDomain,$sController='',$sAction='',$aParameters=array())
	{
		return str_replace('http://'.$_SERVER['SERVER_NAME'],'http://'.urlencode($sSubDomain).'.'.Config::get('domain'),self::url($sController,$sAction,$aParameters));
	}
	
*/
	public function parseURL()
	{
		// omg list causes notice: undefined offset:1 -.-
		@list($v,$sQuery) = explode($this->config['domain'].$this->config['subfolder'],$_SERVER["REQUEST_URI"],2);
		
		if(stristr($_SERVER['SERVER_NAME'],'.'.$this->config['domain']) !== false)
			list($sSubdomain, $v) = explode('.'.$this->config['domain'],$_SERVER['SERVER_NAME'],2);
		else
			$sSubdomain = null;
		
		$aQuery = explode('/',$sQuery);
		$this->sController = array_shift($aQuery);
		$this->sAction = array_shift($aQuery);
		$this->aQuery = $aQuery;
		$this->sSubdomain = $sSubdomain;
		if($this->sAction == '')
			$this->sAction = 'IndexAction';
		else
			$this->sAction .= 'Action';

		if($this->sController == '')
			$this->sController = 'IndexController';
		else
			$this->sController .= 'Controller';
	}


	
/*	public static function forward($sController='',$sAction='',$aParameters=array())
	{
		header('Location: '.self::url($sController,$sAction,$aParameters));
	}
	
	public static function forwardDomain($sSubDomain,$sController='',$sAction='',$aParameters=array())
	{
		header('Location: '.self::domainUrl($sSubDomain,$sController,$sAction,$aParameters));
	}
*/
}
?>
