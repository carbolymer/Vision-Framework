<?php
namespace vsn\libraries;

abstract class AbstractRouter
{
	protected $sAction = null;
	protected $sController = null;
	protected $aQuery = array();
	protected $config = null;
	public $sSubdomain = null;

	public function __construct($aConfig = array())
	{
		if(empty($aConfig))
			$this->config = \vsn\core\Registry::get('config')->HttpRouter;
		else
			$this->config = $aConfig;
	}

	public function loadController()
	{
		$sName = $this->sController;
		$sName = '\app\\'.str_replace('/','\\',$this->config['controller_folder']).'\\'.$sName;
		if(class_exists($sName))
			return new $sName;
		else
			return false;
	}

	public function launch()
	{
		$this->parseURL();
		$oController = $this->loadController();	
		if(!is_object($oController))
		{
			\vsn\core\Event::run('system.404');
			return;
		}
		$sAction = $this->sAction;

		if($this->sSubdomain != null)
			$sAction .= 'Subdomain';

		// TODO: ACL
		\vsn\core\Registry::set($this->sController.':'.$sAction,$oController);
		\vsn\core\Event::add('system.thread.run_controller',
					array(\vsn\core\Registry::get($this->sController.':'.$sAction),$sAction)
				);


	}

	abstract protected function parseURL();
}

?>
