<?php
namespace vsn\libraries;

class SessionFlashdata
{
	private $_sPrefix = null;

	public function __construct($sPrefix)
	{
		$this->_sPrefix = $sPefix.'_flashdata';
	}

	public function __set($sName,$mVar)
    {
		$_SESSION[$this->_sPrefix][$sName] = array( 
				'value'		=> $mVar,
				'delete'	=> 0
				);
    }

	public function __get($sName)
    {
		if(!isset($_SESSION[$this->prefix][$sName]))
			return false;
		return $_SESSION[$this->_sPrefix][$sName]['value'];
    }

	public function __isset($sName)
	{
		return isset($_SESSION[$this->_sPrefix][$sName]);
	}

	public function __unset($sName)
	{
		unset($_SESSION[$this->_sPrefix][$sName]);
	}

	public function gc()
	{
		if(is_array($_SESSION[$this->_sPrefix]))
			foreach($_SESSION[$this->_sPrefix] as $sKey => $aVal)
			{
				if($aVal['delete'] == 1)
					unset($_SESSION[$this->_sPrefix][$sKey]);
				else
					$_SESSION[$this->_sPrefix][$sKey]['delete'] = 1;
			}
	}
};
?>
