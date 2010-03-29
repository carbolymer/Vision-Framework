<?php
namespace vsn\libraries;

abstract class ActiveRecord
{
	public $oDbDriver = null;
	private $_aContainer = array();
	private $_bIsNewRecord = false;
	private $_bIsReadonly = false;
	protected $table_name = null;
	protected static $aClassTables = array();

	public function __construct($aInitValues = array(), $bIsNewRecord = true, $bNoLoad = false)
	{
		$this->oDbDriver = \vsn\core\Registry::get('database');
		$this->_aContainer = $aInitValues;
		$this->_bIsNewRecord = $bIsNewRecord;
		if(!$bIsNewRecord && !$bNoLoad)
			$this->load();
		self::$aClassTables[get_called_class()] = $this->table_name;
	}

	public function readOnly($bVal = true)
	{
		$this->_bIsReadonly = $bVal;
	}

	public function isReadOnly()
	{
		return $this->_bIsReadonly;
	}

	public function isNewRecord()
	{
		return $this->_bIsNewRecord;
	}

	public function save()
	{
		if($this->_bIsNewRecord)
			return $this->insert();
		return $this->update();
	}

	private function update()
	{
		if($this->_bIsNewRecord)
			return $this->insert();
		return $this->oDbDriver->update($this->table_name, $this->_aContainer, "`id` = '".intval($this->id)."'");
	}

	public function delete()
	{
		return $this->oDbDriver->delete($this->table_name, "`id` = '".intval($this->id)."'");
	}
	
	private function insert()
	{
		if(!$this->_bIsNewRecord)
			return $this->update();
		$this->_bIsNewRecord = false;
		return $this->oDbDriver->insert($this->table_name, $this->_aContainer);
	}

	private function load()
	{
		$sCondition = '';	
		foreach($this->_aContainer as $sKey => $sValue)
		{
			if($sCondition != '')
				$sCondition .= ' AND ';
			$sCondition .= "`$sKey` = ".$this->oDbDriver->escape($sValue)."";
		}
		$this->_aContainer = $this->oDbDriver->selectRow(self::$table_name,'*',$sCondition);
	}

	public function __get($sName)
	{
		return $this->_aContainer[$sName];
	}
	public function __set($sName, $mValue)
	{
		$this->_aContainer[$sName] = $mValue;
	}
	public function __isset($sName)
	{
		return isset($this->_aContainer[$sName]);
	}

	public static function __callStatic($sName, $aParameters)
	{// can return array!
		if(preg_match("#^find_by_(\w+)$#si", $sName, $aMatches))
		{
			$sClass = get_called_class();
			$sKey = $aMatches[1];
			$oModel = new $sClass(
						array($sKey => $aParameters[0]),
						false);
			return $oModel;
		}
		elseif(preg_match("#^find_all_by_(\w+)$#si", $sName, $aMatches))
		{
			$sClass = get_called_class();
			$sKey = $aMatches[1];
			$db = \vsn\core\Registry::get('database');
			$aResults = $db->selectRows(self::$aClassTables[$sClass], '*', "`$sKey` = ".$db->escape($aParameters[0])."");
			if(!empty($aResults))
				foreach($aResults as $aR)
					$aModels[] = new $sClass($aR, false, true);
			return $aModels;
		}
		else
			echo "$sName Not Found!";
	}
}
?>
