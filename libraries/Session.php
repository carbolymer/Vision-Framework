<?php
namespace vsn\libraries;

class Session
{
	private $_sPrefix = '_vsn';
	private $config = null;
	public $flashdata = null;

	public function __construct($aConfig = array())
	{
		if(!empty($aConfig))
			$this->config = $aConfig;
		else
			$this->config = \vsn\core\Registry::get('config')->Session;
		session_start();
		$this->flashdata = new SessionFlashdata($this);
	}

	public function regenerateId()
	{
		session_regenerate_id();
	}

    public function destroy()
	{
		// KA-BOOM!
		session_destroy();
	}

	public function gc()
	{
		$this->flashdata->gc();
	}

	public function __set($sName,$mVar)
    {
		$_SESSION[$this->_sPrefix][$sName] = $mVar;
    }

	public function __get($sName)
    {
		if(!isset($_SESSION[$this->prefix][$sName]))
			return false;
		return $_SESSION[$this->_sPrefix][$sName];
    }

	public function __isset($sName)
	{
		return isset($_SESSION[$this->_sPrefix][$sName]);
	}

	public function __unset($sName)
	{
		unset($_SESSION[$this->_sPrefix][$sName]);
	}
};
?>
