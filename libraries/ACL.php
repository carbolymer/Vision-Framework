<?php
/**#@+
 * @version 0.0.1
 */

/**
 * @version 0.0.1
 * @author Carbolymer <carbolymer@o2.pl>
 * @copyright 2007 (C) by Carbolymer
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * ACL Class
 *
 * @author Carbolymer <carbolymer@o2.pl>
 */


//lista kontroli dostepu
//system uprawnien, w trakcie rozwoju
//level - pojedyncze uprawnienie
//role - zestaw uprawnien (np. administrator, moderator)
class ACL
{
// stale
	const LEVEL_ALREADY_EXISTS = 1;
	
	const ROLE_ALREADY_EXISTS = 2;
	
	const LEVEL_DOESNT_EXIST = 3;
	
	const ROLE_DOESNT_EXIST = 4;

/**
 * Array of access levels
 * @var array
 */
	private $_aLevels;
	
/**
 * Array of roles
 * @var array
 */	
	private $_aRoles;
	
	private function __construct()
	{
		$this->load();
	}

/**
 * Singleton
 * @return ACL ACL class Instance
 */	
	public function getInstance()
	{
		static $oInstance;
		if(!is_object($oInstance))
		{
			$oInstance = new self;
		}
		return $oInstance;
	}
/**
 * Add level
 * @param string $sName name of access level to add
 */
	public function addLevel($sName)
	{
		if(isset($this->_aLevels[$sName])) throw new Exception('[ACL] <i>'.$sName.'</i> access level already exists!',self::LEVEL_ALREADY_EXISTS);
		$this->_aLevels[$sName] = pow(2,count($this->_aLevels));
	}
/**
 * Add role
 * @param string $sRole name of role
 * @param array $aLevel access level
 */
	public function addRole($sName,$aLevels)
	{
		if(isset($this->_aLevels[$sName])) throw new Exception('[ACL] Role <i>'.$sName.'</i> already exists. Use ACL::addRoleAccess() for updating priviledges.',self::ROLE_ALREADY_EXISTS);
		foreach($aLevels as $sLevel)
		{
			if(!isset($this->_aLevels[$sLevel])) throw new Exception('[ACL] Level <i>'.$sLevel.'</i> doesn\'t exist.',self::LEVEL_DOESNT_EXIST);
			$this->_aRoles[$sName] |= $this->_aLevels[$sLevel];
		}
	}

/**
 * Remove role from list
 * @param string $sRole name of role to delete
 */
	public function removeRole($sName)
	{
		unset($this->_aRoles[$sName]);
	}

/**
 * Add access level to role
 * @param string $sRole name of role
 * @param string $sLevel access level
 */
	public function addRoleLevel($sRole,$sLevel)
	{
		if(!isset($this->_aLevels[$sLevel])) throw new Exception('[ACL] Level <i>'.$sLevel.'</i> doesn\'t exist.',self::LEVEL_DOESNT_EXIST);
		if(!isset($this->_aRoles[$sRole])) throw new Exception('[ACL] Role <i>'.$sRole.'</i> doesn\'t exist.',self::ROLE_DOESNT_EXIST);
		$this->_aRoles[$sRole] |= $this->_aLevels[$sLevel];
	}

/**
 * Remove access level from role
 * @param string $sRole name of role
 * @param string $sLevel access level to remove
 */
	public function removeRoleLevel($sName,$sLevel)
	{
		if(!isset($this->_aLevels[$sLevel])) throw new Exception('[ACL] Level <i>'.$sLevel.'</i> doesn\'t exist.',self::LEVEL_DOESNT_EXIST);
		if(!isset($this->_aRoles[$sName])) throw new Exception('[ACL] Role <i>'.$sName.'</i> doesn\'t exist.',self::ROLE_DOESNT_EXIST);
		$this->_aRoles[$sName] &= ~$this->_aLevels[$sLevel];
	}
	
/**
 * Check access level
 * @param string $sRole name of role to check access
 * @param string $sLevel access level
 * @return bool Is accessible
 */
	public function isAllowed($sRole,$sLevel)
	{
		if(!isset($this->_aLevels[$sLevel])) throw new Exception('[ACL] Level <i>'.$sLevel.'</i> doesn\'t exist.',self::LEVEL_DOESNT_EXIST);
		if(!isset($this->_aRoles[$sRole])) throw new Exception('[ACL] Role <i>'.$sRole.'</i> doesn\'t exist.',self::ROLE_DOESNT_EXIST);
		return (bool) ($this->_aLevels[$sLevel] & $this->_aRoles[$sRole]);
	}
	
/**
 * Save access controll list as XML file
 * @param string $sFile path of file to save
 */
	public function saveFile($sFile)
	{
		$oXml = new SimpleXMLElement("<?xml version='1.0'?><AccessControlList></AccessControlList>");
		$oLevels = $oXml->addChild('Levels');
		foreach($this->_aLevels as $sName => $iValue)
		{
			$oLevel = $oLevels->addChild('Level',$iValue);
			$oLevel->addAttribute('name',$sName);
		}
		$oRoles = $oXml->addChild('Roles');
		
		foreach($this->_aRoles as $sName => $sLevel)
		{
			$oRole = $oRoles->addChild('Role',$sLevel);
			$oRole->addAttribute('name',$sName);
		}
		file_put_contents($sFile,$oXml->asXML());		
	}
/**
 * Load access controll list from XML file
 * @param string $sFile path of file to load from
 */
	public function loadFile($sFile)
	{
		$oXml = simplexml_load_file($sFile);
		foreach($oXml->Levels->Level as $oLevel)
		{
			$this->_aLevels[strval($oLevel->attributes()->name)] = intval($oLevel);
		}
		
		foreach($oXml->Roles->Role as $oRole)
		{
			$this->_aRoles[strval($oRole->attributes()->name)] = intval($oRole);
		}
	}
	
// j/w tylko ze z bazy danych
	public function load()
	{
		$aLevels = Property::get('database')->selectRows('users_ranks_access_levels');
		$aRoles = Property::get('database')->selectRows('users_ranks');
		
		foreach($aLevels as $aLevel)
		{
			$this->_aLevels[$aLevel['name']] = $aLevel['value'];
		}
		
		foreach($aRoles as $aRole)
		{
			$this->_aRoles[$aRole['id']] = $aRole['rights'];
		}
	}
	
/**
 * Inherit access from other role
 * @param string $sRoleChild name of role which inherits access from parent
 * @param string $sRoleParent name of parent
 */
	public function inheritLevel($sRoleChild,$sRoleParent)
	{
		if(!isset($this->_aRoles[$sRoleChild])) throw new Exception('[ACL] Child role <i>'.$sRoleChild.'</i> doesn\'t exist.',self::ROLE_DOESNT_EXIST);
		if(!isset($this->_aRoles[$sRoleParent])) throw new Exception('[ACL] Parent role <i>'.$sRole.'</i> doesn\'t exist.',self::ROLE_DOESNT_EXIST);
		$this->_aRoles[$sRoleChild] |= $this->_aRoles[$sRoleParent];
	}
}
// wyjatek rzucany przy braku dostepu
class NotAllowed extends Exception{}
?>
