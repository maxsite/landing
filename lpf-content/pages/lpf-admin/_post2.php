<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');

if ($post = mso_check_post('clear_cache'))
{
	if (!_auth()) return 'STOP';
	
	_clear_cache();
	
	echo '<span class="t-green">OK: cache cleared</span>';
	
	return 'STOP';
}

if ($post = mso_check_post('delete_pages', 'page'))
{
	if (!_auth()) return 'STOP';
	
	if ($post['page'])
	{
		foreach($post['page'] as $page)
		{
			// нельзя удалять
			if ($page == 'home' or $page == '404' or $page == 'lpf-admin') continue;
			
			$d = str_replace('\\', '/', PAGES_DIR . $page);
			
			if (is_dir($d)) 
			{
				_delete_files($d, true); // файлы и подкаталоги
				rmdir($d); // основной каталог
			}
		}
	}
	
	// редиректим сюда же
	header('Location: ' . mso_current_url(false, true));
	return 'STOP';
}

# end of file