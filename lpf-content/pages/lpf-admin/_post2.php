<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ($post = mso_check_post('content', 'file_path'))
{
	require_once(ENGINE_DIR . 'additions/auth-session.php');
	if (!mso_auth('')) return;
	
	$file = base64_decode($post['file_path']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = BASE_DIR . 'lpf-content/' . $file;
	
	if (file_exists($file))
	{
		file_put_contents($file, $post['content']);
		echo '<span class="mar10-l t-green t130">✔</span> Saved';
	}
	else
	{
		echo '<span class="mar10-l t-red t130">✖</span> File not found';
	}
	
	return 'STOP';
}
elseif ($post = mso_check_post('load', 'file'))
{
	require_once(ENGINE_DIR . 'additions/auth-session.php');
	if (!mso_auth('')) return;

	$file = base64_decode($post['file']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = BASE_DIR . 'lpf-content/' . $file;

	if (file_exists($file)) echo file_get_contents($file);
	
	return 'STOP';
}

# end of file