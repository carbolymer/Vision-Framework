<?php
namespace \vsn\libraries\security;

// identyfikacjia użytkownika
class AccessControlService
{
	private $config = array();
	private $_oDataContainer = null;

	public function __construct($aConfig = array())
	{
		if(!empty($aConfig))
			$this->config = $aConfig;
		else
		{
			$this->config = \vsn\core\Registry::get('config')->AccessControlService;
		}

		$this->oDataHolder = \vsn\core\Registry::get($aConfig['data_container']);

		if(!is_object($this->oDataHolder))
			throw new \vsn\exceptions\ObjectRequired;
	}

	// autentykacja użytkownika: załadowanie na podstawie danych sesji, lub źródła 
	public function authenticate()
	{
		//$this->_oDataHolder->
	}
};

?>
