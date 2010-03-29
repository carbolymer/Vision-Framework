<?php
namespace vsn\templates;

final class HttpErrorHandler
{
	public static function handle($iCode)
	{
		switch($iCode)
		{
			case '404':
				echo '<h1>Nie ma takiej strony. Internet sie skonczyl.</h1>';
				break;
			case '403':
				echo '<h1>Brak dostępu.</h1>';
				break;
			default:
				echo '<h1>Nie można wyświetlić strony. Kod: '.$iCode.'</h1>';
		}
	}
}

?>
