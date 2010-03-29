<?php
// sterownik bazy danych oferujacy kontakt z baza na wysokim poziome abstrakcji
// funkcje warte obejrzenia:
// getRow() getRows() insert() update()
namespace vsn\libraries;
class Database
{
	public $aHistory = array();
	private $_aParams = array();
	private $_aCache = array();
	private $_oDriver = null;

	private function _extractErrMsg($aErrorInfo)
	{//wyciagniecie informacji o bledzie z tablicy
		return $aErrorInfo[2];
	}
	
	public function __construct($aOptions = null)
	{// konstruktor pobierajacy dane do polaczenia
		if($aOptions == null)
		{
			$aOptions = \vsn\core\Registry::get('config')->Database;
		}

		$this->_aParams = array(
				'host' => $aOptions['host'],
				'port' => $aOptions['port'],
				'name' => $aOptions['name'],
				'user' => $aOptions['user'],
				'password' => $aOptions['password']
			);
	}
	
	private function _connect()
	{// polaczenie wywolywane jedynie gdy jest to konieczne ;]	
		if($this->_oDriver instanceof \PDO)
			return true;
		try
		{// tworzymy polaczenie z baza przez sterownik PDO
			$this->_oDriver = new \PDO('mysql:dbname='.$this->_aParams['name'].';host='.$this->_aParams['host'],$this->_aParams['user'],$this->_aParams['password']);
		}
		catch(PDOException $e)
		{//obsluga bledow
			trigger_error('Cannot connect to mysql ('.$e->getCode().') ! ', E_USER_ERROR);
			$this->_oDriver = null;
			return false;
		}
		return true;

	}
	
	public function query($sSql)
	{// wykonanie zapytania
		if(!($this->_oDriver instanceof PDO))
			if(!$this->_connect())
				return false;
		$iStart = microtime(true);// sprawdzenie czasu zapytania, tak dla statystyk
		$oResult = $this->_oDriver->query($sSql) or trigger_error('Query failed ('.$this->_oDriver->errorCode() .') ! '.$this->_extractErrMsg($this->_oDriver->errorInfo()).(DEBUG_MODE?'<br/>'.$sSql:''), E_USER_ERROR);
		$iTime = microtime(true) - $iStart;
		
		$iRows = $oResult->rowCount();
		// historia zapytan
		$this->aHistory[] = array(
				'sql' => $sSql,
				'time' => $iTime,
				'rows' => $iRows);

		return $oResult;
	}
	
    public function escape($mParam)
    {// escape'owanie wprowadzanych danych
	$this->_connect();
	//mParam moze byc tablica ;]
        if (is_array($mParam))
            return array_map(array($this,'escape'),$mParam);

        if(get_magic_quotes_gpc())
            $mParam = stripslashes($mParam);
		//return trim($this->_oDriver->quote($mParam),"'");
		return $this->_oDriver->quote($mParam);
    }
	
    public function getRows($sSql, $bAssoc = true)
    {// pobieranie wierszy ze zwroconego wyniku mysql
        $bAssoc = ($bAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
		return $this->query($sSql)->fetchAll($bAssoc);
    }
	
    public function getRow($sSql, $bAssoc = true)
    {// j/w tylko jednego wiersza
        $bAssoc = ($bAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_NUM);
        return $this->query($sSql)->fetch_array($bAssoc);
    }
	
	public function countRows($sSql)
	{
		return $this->query($sSql)->rowCount();
	}

    public function insert($sTable, $aValues, $bEscape = true)
    {
        $sCols = '`'.implode('`, `',array_keys($aValues)).'`';
        if ($bEscape)
        {
            $aValues = $this->escape($aValues);
            $sVals = ''.implode(', ',array_values($aValues)).'';
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
                $sSets .= '`'.$sCol.'` = '.$this->escape($sValue).', ';
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
	{//zwraca wynik zapytania, nie tablice
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
