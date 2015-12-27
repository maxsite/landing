<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

if ( isset($_SERVER['HTTP_X_REQUESTED_FILENAME']) and isset($_SERVER['HTTP_X_REQUESTED_FILEUPDIR']) )
{
	require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');
	if (!_auth()) return;
	
	$_fn = $_SERVER['HTTP_X_REQUESTED_FILENAME'];
	$_dr = $_SERVER['HTTP_X_REQUESTED_FILEUPDIR'];

	if (!is_dir(BASEPATH . $_dr)) die('no exist updir');
	
	// файл
	$fn = _slug($_fn);

	// каталог
	$up_dir = BASEPATH . $_dr;

	// file_put_contents(BASEPATH . 'log.txt', $up_dir . $fn); // лог для отладки 

	// Если файл уже существует, то переименовываем новый
	if (file_exists($up_dir . $fn))
	{
		$ext = mso_file_ext($fn);
		$name = substr($fn, 0, strlen($fn) - strlen($ext) - 1);
		
		for ($i = 1; $i < 100; $i++)
		{
			$fn = $name . '-' . $i . '.' . $ext;
			if (!file_exists($up_dir . $fn)) break;
		}
	}
	
	// загрузка 
	file_put_contents( $up_dir . $fn, file_get_contents('php://input') );

	return 'STOP';
}

# end of file