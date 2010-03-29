<?php
namespace vsn\lib;

class Cache
{
	private function __construct(){}
	
/**
* Singleton
* @return Cache Cache class Instance
*/
    public static function getInstance()
    {
        static $oInstance;
        if( !isset($oInstance) )
            $oInstance = new self;
        return $oInstance;
    }

/**
* Save variable to cache
* @param mixed $mVar variable to store
* @param string $sName name of variable which is being stored
*/
    
	public function save($mVar,$sName)
	{
		file_put_contents(Config::get('cacheFolder').'/'.$sName.'.slz',serialize($mVar));
	}
	
/**
* Read variable from cache
* @param string $sName name of variable to get
* @return mixed Value of stored variable
*/
		
	public function load($sName)
	{
		return unserialize(@file_get_contents(Config::get('cacheFolder').'/'.$sName.'.slz'));
	}
}

/**@#-*/
?>
