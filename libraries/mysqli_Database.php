<?php
class Database
{
	public $aHistory = array();
	private $_aParams = array();
	private $_aCache = array();
	private $_oDriver = null;

	public function __construct($sName, $sUser, $sPassword, $sHost, $sPort = 3306)
	{
		$this->_aParams = array(
				'host' => $sHost,
				'port' => $sPort,
				'name' => $sName,
				'user' => $sUser,
				'password' => $sPassword
			);
	}
	
	private function _connect()
	{
		if($this->_oDriver instanceof mysqli)
			return true;
		$this->_oDriver = new mysqli($this->_aParams['host'],$this->_aParams['user'],$this->_aParams['password'],$this->_aParams['name'],$this->_aParams['port']);
		if ($this->_oDriver->connect_error) // THIS ERROR SUPPORT REQUIRES PHP 5.2.9
		{
			trigger_error('Cannot connect to mysql ('.$this->_oDriver->connect_errno .') ! '.$this->_oDriver->connect_error, E_USER_ERROR);
			$this->_oDriver = null;
			return false;
		}
		return true;

	}
	
	public function disconnect()
	{
		if($this->_oDriver instanceof mysqli)
			return $this->_oDriver->close();
		return true;
	}
	
	public function query($sSql)
	{
		if(!($this->_oDriver instanceof mysqli))
			if(!$this->_connect())
				return false;
		$iStart = microtime(true);
		$oResult = $this->_oDriver->query($sSql) or trigger_error('Query fail ('.$this->_oDriver->errno .') ! '.$this->_oDriver->error.(DEBUG_MODE?'<br/>'.$sSql:''), E_USER_ERROR);
		$iTime = microtime(true) - $iStart;
		
		if($oResult instanceof MySQLi_Result)
			$iRows = $oResult->num_rows;
		else 
			$iRows = $this->_oDriver->affected_rows;
		
		$this->aHistory[] = array(
				'sql' => $sSql,
				'time' => $iTime,
				'rows' => $iRows);

		//if($oResult instanceof MySQLi_Result)
			return $oResult;
		//trigger_error('Query fail ('.$this->_oDriver->errno .') ! '.$this->_oDriver->error, E_USER_ERROR);
	}
	
    public function escape($mParam)
    {
		$this->_connect();
        if (is_array($mParam))
            return array_map(array($this,'escape'),$mParam);

        if (get_magic_quotes_gpc())
            $mParam = stripslashes($mParam);

        return $this->_oDriver->real_escape_string($mParam);
    }
	
    public function getRows($sSql, $bAssoc = true)
    {
        $bAssoc = ($bAssoc ? MYSQLI_ASSOC : MYSQLI_NUM);
		/*$oResult = $this->query($sSql);
		$aResult->$oResult->fetch_all($bAssoc);
		$oResult->close();
        return $aResult;*/
		return $this->query($sSql)->fetch_all($bAssoc);
    }
	
    public function getRow($sSql, $bAssoc = true)
    {
        $bAssoc = ($bAssoc ? MYSQLI_ASSOC : MYSQLI_NUM);
		/*$oResult = $this->query($sSql);
		$aResult = $oResult->fetch_array($bAssoc);
		$oResult->close();
        return $aResult;*/
        return $this->query($sSql)->fetch_array($bAssoc);
    }
	
	public function countRows($sSql)
	{
		/*$oResult = $this->query($sSql);
		$iNum = $oResult->num_rows;
		$oResult->close();
		return $iNum;*/
		return $this->query($sSql)->num_rows;
	}
	
    public function insert($sTable, $aValues, $bEscape = true)
    {
        $sCols = '`'.implode('`, `',array_keys($aValues)).'`';
        if ($bEscape)
        {
            $aValues = $this->escape($aValues);
            $sVals = '"'.implode('","',array_values($aValues)).'"';
        }
        else
            $sVals = implode(',',array_values($aValues));

        $sSql = 'INSERT INTO `'.$sTable.'` '.
                '        ('.$sCols.')'.
                ' VALUES ('.$sVals.')';
		return $this->query($sSql);
    }

    public function update($sTable, $aValues, $sCond, $bEscape=true)
    {
        if (!is_array($aValues))
            return false;

        $sSets = '';
        foreach ($aValues as $sCol => $sValue)
        {
            if ($bEscape)
                $sSets .= '`'.$sCol.'` = "'.$this->escape($sValue).'", ';
            else
                $sSets .= '`'.$sCol.'` = '.$sValue.', ';
        }
        $sSets[strlen($sSets)-2]='  '; //replace trailing ','
        $sSql = 'UPDATE `'.$sTable.'` SET '.$sSets.' WHERE '.$sCond;
        return $this->query($sSql);
    }
	
	public function selectRows($sTable, $mCols = '*', $sCond='', $iLimRows=0, $iLimOff=0, $sKeyField = '', $bAssoc = true)
    {
		if (is_array($mCols))
            $mCols = implode('`,`',$mCols);

		//FILTER INPUT		
		if($mCols != '*') $mCols = '`'.$mCols.'`';
        $sSql = 'SELECT '.$mCols.' FROM `'.$sTable.'`';
        if ($sCond)
            $sSql .= ' WHERE '.$sCond;
        if ($sKeyField)
            $sSql .= ' ORDER BY '.$sKeyField;
        if ($iLimRows > 0 ) {
            $sSql .= '   LIMIT '.intval($iLimOff).','.intval($iLimRows);
        }

        return $this->getRows($sSql,$bAssoc);
    }

    public function selectRow($sTable, $mCols = '*', $sCond='')
	{
		$aResult = $this->selectRows($sTable, $mCols, $sCond, 1);
		if(!is_array($aResult)) return NULL;
        return current($aResult);
    }
	
    public function delete($sTable, $sQuery)
    {
    	$this->query("DELETE FROM ". $sTable ." WHERE ". $sQuery ."");
    }
	
	
}
?>