<?php
namespace vsn\libraries;

class HttpView
{
	private $_aVars = array();
	private $_sName = null;
	private $config = null;
    
	public function __construct()
	{
		$this->config = \vsn\core\Registry::get('config')->HttpView;
		$oRouterConfig = \vsn\core\Registry::get('config')->HttpRouter;
		// make url base visible in templates
		$this->_aVars['_base'] = 'http://'.$oRouterConfig['domain'].$oRouterConfig['subfolder'].$this->config['view_folder'];
	}

	public function __set($sName,$mValue)
	{
		$this->_aVars[$sName] = $mValue;
	}

    public function __get($sName)
    {
        if(isset($this->_aVars[$sName]))
			return $this->_aVars[$sName];
		else
			return null;
    }

    public function __isset($sName)
    {
        return isset($this->_aVars[$sName]);
    }

	public function sendHeaders()
	{
		; // send headers to teh browsa
	}

	public function render($sName)
	{
		$this->_sName = $sName;
	}

	public function display()
	{
		if(!file_exists($this->config['view_folder'].'/'.$this->_sName.'.tpl') && $this->_sName != null) 
			throw new Exception('['.get_class($this).'] View '.$this->_sName.' does not exist!');
		include_once($this->config['view_folder'].'/'.$this->_sName.$this->config['view_file_ext']);
	}
};

?>
