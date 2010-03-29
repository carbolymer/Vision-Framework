#!/usr/bin/php
<?php
if(PHP_SAPI != 'cli')
{
	die('Only CLI mode allowed.');
}

$rMemory = shm_attach(100);

while(true)
{
	if(shm_has_var($rMemory,100))
	{
		$aNotify = shm_get_var($rMemory,100);
		shm_remove_var($rMemory,100);
		//echo ">: Sending notify [".$aNotify['summary'].' : '.$aNotify['body'].']'."\n";
		if($aNotify['icon'] != null)
			$sIcon = '-i '.escapeshellarg($aNotify['icon']).' ';
		$sCommand = 'notify-send '.$sIcon.escapeshellarg($aNotify['summary']).' '.escapeshellarg($aNotify['body']);
		exec($sCommand);
	}
	usleep(500000);
}
?>
