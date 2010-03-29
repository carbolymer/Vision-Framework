<?php
//Klasa do przechwytywania uploadowanych plikow
class FileUpload
{
	private $_aAllowedMimeTypes = array();
	private $_sName = NULL;
	private $_sDestination = NULL;
	private $_iSize = NULL;
	private $_aValidators = array();
	//to update
	private $aMimeTypes = array(
		'text/plain' => 'txt',
		'text/enriched' => 'txt',
		'text/html' => 'html',
		'audio/basic' => 'snd',
		'image/gif' => 'gif',
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'video/mpeg' => 'mpg'
	);

	//sName to nazwa przekazanego pliku
	public function __construct($sName)
	{
		$this->_sName = $sName;
		$this->_iSize = $this->returnBytes(ini_get('upload_max_filesize'));
	}
	
	// dodanie dozwolonego typu mime
	public function addMime($sMimeType)
	{
		$this->_aAllowedMimeTypes[] = $sMimeType;
	}
	
	// folder path, where file will be located
	public function setDestination($sDestination)
	{
		$this->_sDestination = $sDestination;
	}
	//dodanie walidatora do pliku
	//prototyp funkcji:
	//validator($sFilename)
	public function addValidator($sType, $mCallback)
	{
		$this->_aValidators[$sType][] = $mCallback;
	}
	//ustawienie maksymalnego rozmiaru do przyjecia
	public function setMaxSize($iSize)
	{
		if($iSize > $this->returnBytes(ini_get('post_max_size')))
			trigger_error('['.get_class($this).'] Size set is larger than post_max_size in php.ini file.');
		if($iSize > $this->returnBytes(ini_get('upload_max_filesize')))
			trigger_error('['.get_class($this).'] Size set is larger than upload_max_filesize in php.ini file.');
		$this->_iSize = $iSize;
	}

	//obsłużenie pliku
	public function handle(&$sFileName)
	{
		$aFile = Request::getFile($this->_sName);
		if(is_uploaded_file($aFile['tmp_name']) && $aFile['error'] == UPLOAD_ERR_OK && $aFile['size'] > 0 && $aFile['size'] <= $this->_iSize)
		{
			if(!in_array($aFile['type'],$this->_aAllowedMimeTypes))
				return 'invalid_mime';
			$sType = reset(explode('/',$aFile['type']));
			// przetwarzanie walidatorami
			if(is_array(@$this->_aValidators[$sType]))
				foreach($this->_aValidators[$sType] as $mValidator)
				{
					$result = call_user_func($mValidator,$aFile['tmp_name']);
					if($result !== true)
						return $result;
				}
			do//losowanie unikalnej nazwy pliku
			{
				$sFileName = md5(mt_rand(0,99999).time()).'.'.$this->aMimeTypes[$aFile['type']];
			}while(file_exists($this->_sDestination.'/'.$sFileName));
			
			if(!move_uploaded_file($aFile['tmp_name'], $this->_sDestination.'/'.$sFileName))
			{
				return 'could_not_move_file';
			}
			return true;
		}
		elseif($aFile['size'] >= $this->_iSize)
			return 'size_exceeded';
		else
			return $aFile['error'];
	}

	// przeliczanie przyrostkow na bajty
	private function returnBytes($sValue) 
	{
		$sValue = trim($sValue);
		$sLast = strtolower(substr($sValue, -1));
		
		if($sLast == 'g')
			return $sValue*1024*1024*1024;
		if($sLast == 'm')
			return $sValue*1024*1024;
		if($sLast == 'k')
			return $sValue*1024;
	}
}
?>
