<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(с) Landing Page Framework (LPF)
	(c) MAX — http://lpf.maxsite.com.ua/
*/

require_once(ENGINE_DIR . 'engine.php');

if ($fn = mso_fe(BASEPATH . 'lpf-content/config/config.php')) require($fn);

init();

if ($fn = mso_fe(CURRENT_PAGE_DIR . '_server.php'))
{
	if ((include $fn) == 'STOP') exit;
}

if ($fn = mso_fe(BASEPATH . 'lpf-content/config/variables.php')) require($fn);

if ($fn = mso_fe(CURRENT_PAGE_DIR . 'variables.php')) 
	require($fn);
else 
	mso_get_yaml(CURRENT_PAGE_FILE);

if ($fn = mso_fe(CURRENT_PAGE_DIR . 'functions.php')) require($fn);

if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) or (isset($_POST) and $_POST) )
{
	if ($fn = mso_fe(CURRENT_PAGE_DIR . '_post2.php')) 
	{
		if ((include $fn) == 'STOP') exit;
	}
	
	if ($fn = mso_fe(CURRENT_PAGE_DIR . '_post.php'))
	{
		require($fn);
		exit;
	}
}

if ($VAR['no_output_only_file'] and $fn = mso_fe(CURRENT_PAGE_DIR . $VAR['no_output_only_file'])) 
{ 
	require($fn);
	exit;
}

init2();

if ($VAR['generate_static_page']) ob_start();

if ($VAR['before_file'] and $fn = mso_fe($VAR['before_file'])) require($fn);

#end of file