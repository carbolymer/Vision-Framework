<?php
namespace vsn\libraries;
abstract class AbstractController
{
	//sprawdzenie praw dostępu i wyrzucenie wyjątku przy ich braku
	protected function checkAuth($sLevel)
	{
		// DEPRECATED
		if(!ACL::getInstance()->isAllowed(Property::get('user')->rank_id,$sLevel))
			throw new NotAllowed;
	}
	//phpinfo dostepne z kazdego kontrolera
	public function phpinfoAction()
	{
		if(DEBUG_MODE)
			phpinfo();
		else
			Router::forward('Index');
	}

	//zabezpieczenie na wypadek nieistniejącej akcji w kontrolerze
	public function __call($sMethod,$aParams)
	{
		if(DEBUG_MODE)
        	{
        		throw new Exception('['.get_class($this).'] Method '.$sMethod.' does not exist!');
        	}else $this->IndexAction();
	}
}
?>
