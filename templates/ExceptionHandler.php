<?php
namespace vsn\templates;

final class ExceptionHandler
{
	public static function handle($oException)
	{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-2" />
<title>Skeleton :: Halt</title>
<style type="text/css">
body
{
	width: 800px;
	margin: 0 auto;
	text-align: center;
	font: normal 11px Verdana;
	color: #EEEEEE;
	background: #056A90;
}

div#wrap
{
	background: #FFFFFF;
	color: #333333;
	margin-top: 100px;
	border: solid 2px #054475;
	border-top-width: 7px;
	border-bottom-width: 7px;
	padding: 20px;
	text-align: left;
}
h1
{
	text-align: left;
	font: bolder 25px sans-serif;
	color: #6D000D;
	padding-bottom: 30px;
	border-bottom: solid 1px #DEDEDE;
}
div.message
{
	margin: 10px;
	border: solid 1px #DEDEDE;
	background: #EEEEEE;
	padding: 4px;
	font: normal 10px Verdana;
	color: #000000;
}
p.line
{
	margin: 5px;
	margin-left: -15px;
	margin-bottom: 20px;
	background: #CFCFCF;
	padding: 3px;
	padding-left: 5px;
	padding-right: 5px;
	font: normal 11px Lucida Console;
	color: #333333;
}

li
{
	magin: 0;
	padding: 0px;
}
ol
{
	margin: 0;
	padding-left: 20px;
}
span.xmsg
{
	font: bold 15px Verdana;
	text-align: center;
	display: block;
}
</style>

</head>
<body>
<div id="wrap">
<h1>Skeleton halt: <i>'.get_class($oException).'</i> Error</h1>
Message:
<div class="message">
<span class="xmsg">'.($oException->getMessage()!==''?$oException->getMessage():'No error message.').'</span>
</div>
'.($oException->getCode()!==0?'
Code:
<div class="message">
<b>'.$oException->getCode().'</b>
</div>':'').'
Backtrace:
<div class="message">
<ol>';

$aTrace = $oException->getTrace();
foreach($aTrace as  $aLine)
	echo '<li>'.@$aLine['file'].' : '.@$aLine['line'].' <p class="line">'.@$aLine['function'].'('.@implode(', ',@$aLine['args']).')</p></li>
';
echo'
</ol>';
\vsn\libraries\Profiler::stop('APPLICATION');
$a = \vsn\libraries\Profiler::getAllTimestamps();
echo 'Time: '.$a['APPLICATION']['time'];
echo 's
</div>
</div>
</body>
</html>';
	}
}
?>
