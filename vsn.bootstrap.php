<?php
namespace vsn\core;

function autoload($sClassName)
{
	Event::run('system.autoload',array($sClassName));
}

function scream($msg)
{
	echo $msg.'<br/>';
}

function log($sString)
{
	file_put_contents("log://run.log",$sString,FILE_APPEND);
}

function prepare($sConfigFile)
{
	$sFrameworkPath = dirname(__FILE__);
	require_once $sFrameworkPath.'/core/Config.php';
	$oConfig = Config::loadStagable($sConfigFile);

	// Stage 00	
	$oConfig->loadStage(00);
	$oConfig->core['framework_path'] = $sFrameworkPath;
	error_reporting($oConfig->core['error_reporting']);

	// before everything starts, we need some core libs
	require_once $sFrameworkPath.'/core/Event.php';
	require_once $sFrameworkPath.'/core/Loader.php';
	require_once $sFrameworkPath.'/core/Registry.php';

	Registry::set('config',$oConfig);
	//var_dump(Registry::get('config'));

	Event::add('system.autoload','\vsn\core\Loader::load');
	spl_autoload_register(__NAMESPACE__.'\autoload');

	// strange registry behaviour!!!
	$oConfig->loadStage(10);
	//update
	//Registry::set('config',$oConfig);
	$oConfig->loadStage(20);
	// update registry
	//Registry::set('config',$oConfig);
	

	// some class mappings
	Registry::$aMap = $oConfig->registry;
	

	Event::init($oConfig->event);
	
	
	
}

function init()
{
	Event::run('system.thread.loader');
	Event::run('system.thread.router');
	Event::run('system.thread.pre_controller');
	Event::run('system.thread.run_controller', array(), true);
	Event::run('system.thread.post_controller');
	Event::run('system.thread.send_headers');
	Event::run('system.thread.display');
	Event::run('system.thread.shutdown');
}

?>
