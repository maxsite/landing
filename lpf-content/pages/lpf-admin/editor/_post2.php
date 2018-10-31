<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');

if ($post = mso_check_post('content', 'file_path'))
{
	if (!_auth()) return 'STOP';
	
	$file = base64_decode($post['file_path']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$page_a = $file; //  pages/about/index.php — определим страницу по адресу
	$file = BASE_DIR . $file;
	
	if (file_exists($file))
	{
		$url = '';
		
		// pr(_ss($file));
		// pr(_ss(PAGES_DIR));
		// pr(_ss(BASE_DIR));
		
		if(strpos(_ss($file), _ss(PAGES_DIR)) !== false) // это какая-то pages
		{
			
			$page_a = pathinfo($file, PATHINFO_DIRNAME); // каталог
			$page_a = str_replace(_ss(PAGES_DIR), '', _ss($page_a)); // убрать pages_dir
			$page_a = explode('/', $page_a); // первый сегмент
			$page_a = $page_a[0]; // первый элемент
		
			$url = ' <a class="mar20-l" target="_blank" href="' . BASE_URL . $page_a . '">View page</a>';
		}
		
		/*
		// страница определяется как первый сегмент после pages/
		if (strpos($page_a, 'pages/') === 0)
		{
			$page_a = str_replace('pages/', '', $page_a);
			$page_a = @strstr($page_a, '/', true);
		}
		else 
			$page_a = '';
		
		if ($page_a) $url = ' <a target="_blank" href="' . BASE_URL . $page_a . '">View page</a>';
		*/
		
		
		file_put_contents($file, $post['content']);
		
		_clear_cache();
		
		echo '<span class="mar10-l t-green t130">✔</span> Saved' . $url;
	}
	else
	{
		echo '<span class="mar10-l t-red t130">✖</span> File not found';
	}
	
	return 'STOP';
}
elseif ($post = mso_check_post('load', 'file'))
{
	
	if (!_auth()) return 'STOP';

	$file = base64_decode($post['file']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	// pr($file);
	$file = BASE_DIR . $file;

	
	if (file_exists($file)) echo file_get_contents($file);
	
	return 'STOP';
}
elseif ($post = mso_check_post('delete_file'))
{
	if (!_auth()) return 'STOP';

	$file = base64_decode($post['delete_file']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = BASE_DIR . $file;

	// pr($file);
	
	if (file_exists($file)) @unlink($file);
	
	return 'STOP';
}




# end of file