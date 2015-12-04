<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(ENGINE_DIR . 'additions/auth-session.php');

if (isset($_POST['content']) and isset($_POST['file_path']) and $_POST['file_path'])
{
	if (!mso_auth('')) return;
	
	$file = base64_decode($_POST['file_path']);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = BASE_DIR . 'lpf-content/' . $file;
	
	if (file_exists($file)) 
	{
		file_put_contents($file, $_POST['content']);
		echo '<span class="mar10-l t-green t130">✔</span> Saved';
	}
	else
	{
		echo '<span class="mar10-l t-red t130">✖</span> File not found';
	}
}
elseif (isset($_POST['load']) and isset($_POST['file']) and $post = $_POST['file'])
{
	if (!mso_auth('')) return;

	$file = base64_decode($post);
	$file = str_replace('~', '-', $file);
	$file = str_replace('\\', '-', $file);
	$file = BASE_DIR . 'lpf-content/' . $file;

	if (file_exists($file)) echo file_get_contents($file);
}

# end of file