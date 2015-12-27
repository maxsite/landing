<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(PAGES_DIR . CURRENT_PAGE_ROOT . '/functions.php');

if ($post = mso_check_post('content', 'file_path'))
{
	if (!_auth()) return 'STOP';
	
	$file = base64_decode($post['file_path']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$page_a = $file; //  pages/about/index.php — определим страницу по адресу
	$file = BASE_DIR . 'lpf-content/' . $file;
	
	if (file_exists($file))
	{
		$url = '';
		
		// страница определяется как первый сегмент после pages/
		if (strpos($page_a, 'pages/') === 0)
		{
			$page_a = str_replace('pages/', '', $page_a);
			$page_a = @strstr($page_a, '/', true);
		}
		else 
			$page_a = '';
		
		if ($page_a) $url = ' <a target="_blank" href="' . BASE_URL . $page_a . '">View page</a>';
		
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
	$file = BASE_DIR . 'lpf-content/' . $file;

	if (file_exists($file)) echo file_get_contents($file);
	
	return 'STOP';
}

# end of file